#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de Migración de Productos y Categorías
De WordPress/WooCommerce a StyloFitness Database

Este script migra:
- Categorías de productos (wp_terms -> product_categories)
- Productos (wp_posts + wp_postmeta -> products)
- Metadatos de productos (precios, stock, SKU, etc.)
"""

import mysql.connector
import json
import re
import random
from datetime import datetime
from typing import Dict, List, Optional, Tuple

class StyloFitnessMigration:
    def __init__(self, wp_config: Dict, sf_config: Dict):
        """Inicializa las conexiones a las bases de datos"""
        self.wp_conn = mysql.connector.connect(**wp_config)
        self.sf_conn = mysql.connector.connect(**sf_config)
        self.category_mapping = {}
        
    def __del__(self):
        """Cierra las conexiones"""
        if hasattr(self, 'wp_conn'):
            self.wp_conn.close()
        if hasattr(self, 'sf_conn'):
            self.sf_conn.close()
    
    def migrate(self):
        """Ejecuta la migración completa"""
        print("=== INICIANDO MIGRACIÓN DE STYLOFITNESS ===")
        
        try:
            self.migrate_categories()
            self.migrate_products()
            print("\n=== MIGRACIÓN COMPLETADA EXITOSAMENTE ===")
        except Exception as e:
            print(f"ERROR: {e}")
            raise e
    
    def migrate_categories(self):
        """Migra las categorías de productos"""
        print("\n--- Migrando Categorías ---")
        
        # Categorías principales identificadas del análisis
        categories = [
            {'term_id': 18, 'name': 'PROTEÍNAS WHEY', 'slug': 'whey'},
            {'term_id': 19, 'name': 'GANADORES DE MASA', 'slug': 'ganadores-de-masa'},
            {'term_id': 20, 'name': 'PROTEINAS ISOLATADAS', 'slug': 'proteinas-isolatadas'},
            {'term_id': 21, 'name': 'PRE ENTRENOS Y ÓXIDO NITRICO', 'slug': 'pre-entrenos'},
            {'term_id': 22, 'name': 'PRECURSOR DE LA TESTO', 'slug': 'testo'},
            {'term_id': 23, 'name': 'MULTIVITAMINICO Colágenos OMEGAS', 'slug': 'multivitaminico'},
            {'term_id': 24, 'name': 'QUEMADORES DE GRASA', 'slug': 'quemadores'},
            {'term_id': 25, 'name': 'AMINOÁCIDOS Y BCAA', 'slug': 'aminoacidos-y-bcaa'},
            {'term_id': 26, 'name': 'CREATINAS Y GLUTAMINAS', 'slug': 'creatinas-y-glutaminas'},
            {'term_id': 27, 'name': 'PROTECTOR HEPÁTICO', 'slug': 'protector-hepatico'}
        ]
        
        cursor = self.sf_conn.cursor()
        
        insert_sql = """
            INSERT INTO product_categories (name, slug, description, is_active, created_at, updated_at)
            VALUES (%s, %s, %s, 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            updated_at = NOW()
        """
        
        for category in categories:
            description = self.generate_category_description(category['name'])
            
            cursor.execute(insert_sql, (
                category['name'],
                category['slug'],
                description
            ))
            
            # Obtener el ID de la categoría insertada
            new_category_id = cursor.lastrowid
            if not new_category_id:
                # Si no hay lastrowid, buscar por slug
                cursor.execute("SELECT id FROM product_categories WHERE slug = %s", (category['slug'],))
                result = cursor.fetchone()
                new_category_id = result[0] if result else None
            
            self.category_mapping[category['term_id']] = new_category_id
            
            print(f"✓ Categoría migrada: {category['name']} (ID: {new_category_id})")
        
        self.sf_conn.commit()
        cursor.close()
        
        print(f"Total categorías migradas: {len(categories)}")
    
    def migrate_products(self):
        """Migra los productos"""
        print("\n--- Migrando Productos ---")
        
        # Obtener productos de WordPress
        wp_cursor = self.wp_conn.cursor(dictionary=True)
        
        sql = """
            SELECT p.ID, p.post_title, p.post_content, p.post_excerpt, p.post_name as slug,
                   p.post_date, p.post_status
            FROM wp_posts p
            WHERE p.post_type = 'product'
            AND p.post_status IN ('publish', 'draft')
            ORDER BY p.ID
        """
        
        wp_cursor.execute(sql)
        products = wp_cursor.fetchall()
        wp_cursor.close()
        
        sf_cursor = self.sf_conn.cursor()
        
        insert_sql = """
            INSERT INTO products (
                category_id, name, slug, description, short_description, sku, price, sale_price,
                stock_quantity, weight, images, brand, is_featured, is_active, views_count,
                sales_count, created_at, updated_at
            ) VALUES (
                %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 0, 0, %s, NOW()
            )
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            price = VALUES(price),
            updated_at = NOW()
        """
        
        for product in products:
            # Obtener metadatos del producto
            metadata = self.get_product_metadata(product['ID'])
            
            # Obtener categoría del producto
            category_id = self.get_product_category(product['ID'])
            
            # Procesar datos del producto
            product_data = self.process_product_data(product, metadata)
            
            sf_cursor.execute(insert_sql, (
                category_id,
                product_data['name'],
                product_data['slug'],
                product_data['description'],
                product_data['short_description'],
                product_data['sku'],
                product_data['price'],
                product_data['sale_price'],
                product_data['stock_quantity'],
                product_data['weight'],
                product_data['images'],
                product_data['brand'],
                product_data['is_featured'],
                product_data['is_active'],
                product['post_date']
            ))
            
            print(f"✓ Producto migrado: {product_data['name']}")
        
        self.sf_conn.commit()
        sf_cursor.close()
        
        print(f"Total productos migrados: {len(products)}")
    
    def get_product_metadata(self, product_id: int) -> Dict:
        """Obtiene los metadatos de un producto"""
        cursor = self.wp_conn.cursor(dictionary=True)
        
        sql = """
            SELECT meta_key, meta_value
            FROM wp_postmeta
            WHERE post_id = %s
            AND meta_key IN ('_price', '_regular_price', '_sale_price', '_sku', '_stock', 
                           '_stock_status', '_manage_stock', '_weight', '_featured')
        """
        
        cursor.execute(sql, (product_id,))
        metadata_rows = cursor.fetchall()
        cursor.close()
        
        metadata = {}
        for row in metadata_rows:
            metadata[row['meta_key']] = row['meta_value']
        
        return metadata
    
    def get_product_category(self, product_id: int) -> int:
        """Obtiene la categoría de un producto"""
        cursor = self.wp_conn.cursor()
        
        sql = """
            SELECT tt.term_id
            FROM wp_term_relationships tr
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = %s
            AND tt.term_id IN (18, 19, 20, 21, 22, 23, 24, 25, 26, 27)
            LIMIT 1
        """
        
        cursor.execute(sql, (product_id,))
        result = cursor.fetchone()
        cursor.close()
        
        if result and result[0] in self.category_mapping:
            return self.category_mapping[result[0]]
        
        return 1  # Categoría por defecto
    
    def process_product_data(self, product: Dict, metadata: Dict) -> Dict:
        """Procesa los datos del producto"""
        # Limpiar y procesar el contenido
        description = self.clean_html(product['post_content'] or '')
        short_description = self.clean_html(product['post_excerpt'] or '')
        
        # Si no hay descripción corta, crear una desde la descripción
        if not short_description and description:
            short_description = self.create_short_description(description)
        
        # Procesar precios
        price = float(metadata.get('_price') or metadata.get('_regular_price') or 0)
        sale_price = float(metadata.get('_sale_price')) if metadata.get('_sale_price') else None
        
        # Procesar stock
        stock_quantity = int(metadata.get('_stock') or 100)
        if stock_quantity <= 0:
            stock_quantity = 100 if metadata.get('_stock_status', 'instock') == 'instock' else 0
        
        # Generar SKU si no existe
        sku = metadata.get('_sku') or self.generate_sku(product['post_title'])
        
        # Determinar marca desde el título
        brand = self.extract_brand(product['post_title'])
        
        return {
            'name': product['post_title'],
            'slug': product['slug'],
            'description': description,
            'short_description': short_description,
            'sku': sku,
            'price': price,
            'sale_price': sale_price,
            'stock_quantity': stock_quantity,
            'weight': float(metadata.get('_weight') or 0),
            'images': json.dumps([]),  # Las imágenes se migrarían por separado
            'brand': brand,
            'is_featured': 1 if metadata.get('_featured') == 'yes' else 0,
            'is_active': 1 if product['post_status'] == 'publish' else 0
        }
    
    def clean_html(self, content: str) -> str:
        """Limpia el HTML del contenido"""
        # Remover shortcodes de WordPress
        content = re.sub(r'\[.*?\]', '', content)
        
        # Limpiar HTML básico pero mantener estructura
        allowed_tags = ['<p>', '</p>', '<br>', '<strong>', '</strong>', '<b>', '</b>', 
                       '<em>', '</em>', '<i>', '</i>', '<ul>', '</ul>', '<li>', '</li>',
                       '<h1>', '</h1>', '<h2>', '</h2>', '<h3>', '</h3>', '<h4>', '</h4>',
                       '<h5>', '</h5>', '<h6>', '</h6>']
        
        # Remover todos los tags excepto los permitidos
        content = re.sub(r'<(?!/?(?:' + '|'.join([tag.strip('<>') for tag in allowed_tags]) + ')\b)[^>]*>', '', content)
        
        # Limpiar espacios extra
        content = re.sub(r'\s+', ' ', content)
        content = content.strip()
        
        return content
    
    def create_short_description(self, description: str) -> str:
        """Crea una descripción corta desde la descripción completa"""
        # Remover tags HTML para contar caracteres
        text = re.sub(r'<[^>]+>', '', description)
        text = re.sub(r'\s+', ' ', text)
        
        if len(text) <= 200:
            return text
        
        short = text[:200]
        last_space = short.rfind(' ')
        
        if last_space != -1:
            short = short[:last_space]
        
        return short + '...'
    
    def generate_sku(self, title: str) -> str:
        """Genera un SKU basado en el título del producto"""
        sku = re.sub(r'[^a-zA-Z0-9]', '', title.upper())
        sku = sku[:10]
        return sku + str(random.randint(100, 999))
    
    def extract_brand(self, title: str) -> str:
        """Extrae la marca del título del producto"""
        brands = ['CARNIVOR', 'MUTANT', 'PROSTAR', 'NITROTECH', 'MUSCLETECH', 'DIMATIZE', 
                 'RONNIE COLEMAN', 'LAB', 'ISOLATE', 'MASS', 'WHEY']
        
        title_upper = title.upper()
        
        for brand in brands:
            if brand in title_upper:
                return brand
        
        # Si no se encuentra marca, usar la primera palabra
        words = title.split()
        return words[0].capitalize() if words else 'Generic'
    
    def generate_category_description(self, category_name: str) -> str:
        """Genera descripción para categorías"""
        descriptions = {
            'PROTEÍNAS WHEY': 'Proteínas de suero de leche de alta calidad para el desarrollo muscular.',
            'GANADORES DE MASA': 'Suplementos hipercalóricos para ganar peso y masa muscular.',
            'PROTEINAS ISOLATADAS': 'Proteínas aisladas de máxima pureza y absorción rápida.',
            'PRE ENTRENOS Y ÓXIDO NITRICO': 'Suplementos pre-entrenamiento para máximo rendimiento.',
            'PRECURSOR DE LA TESTO': 'Suplementos naturales para optimizar niveles hormonales.',
            'MULTIVITAMINICO Colágenos OMEGAS': 'Vitaminas, minerales y suplementos para salud general.',
            'QUEMADORES DE GRASA': 'Suplementos termogénicos para pérdida de grasa.',
            'AMINOÁCIDOS Y BCAA': 'Aminoácidos esenciales para recuperación muscular.',
            'CREATINAS Y GLUTAMINAS': 'Suplementos para fuerza, potencia y recuperación.',
            'PROTECTOR HEPÁTICO': 'Suplementos para protección y salud hepática.'
        }
        
        return descriptions.get(category_name, 'Categoría de productos de nutrición deportiva.')

def main():
    """Función principal"""
    # Configuración de bases de datos
    wp_config = {
        'host': 'localhost',
        'database': 'wordpress_db',
        'user': 'root',
        'password': '',
        'charset': 'utf8mb4'
    }
    
    sf_config = {
        'host': 'localhost',
        'database': 'stylofitness_db',
        'user': 'root',
        'password': '',
        'charset': 'utf8mb4'
    }
    
    # Ejecutar migración
    try:
        migration = StyloFitnessMigration(wp_config, sf_config)
        migration.migrate()
    except Exception as e:
        print(f"Error en la migración: {e}")
        return 1
    
    return 0

if __name__ == '__main__':
    exit(main())