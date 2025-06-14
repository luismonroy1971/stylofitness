   <head>
    <style>
        /* =============================================
            FOOTER ULTRA MODERNO Y PROFESIONAL
            ============================================= */

            .footer-enhanced-pro {
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
                position: relative;
                overflow: hidden;
                margin-top: 4rem;
                padding: 0;
                color: var(--white);
            }

            .footer-background-pro {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
            }

            .footer-overlay-pro {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
            }

            .footer-pattern {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: 
                    radial-gradient(circle at 25% 25%, rgba(255, 107, 0, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 75% 75%, rgba(255, 107, 0, 0.05) 0%, transparent 50%);
                opacity: 0.3;
            }

            .footer-glow {
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 107, 0, 0.05) 0%, transparent 70%);
                animation: footerGlow 8s ease-in-out infinite alternate;
            }

            .footer-particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 2;
                pointer-events: none;
            }

            .footer-particles .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 107, 0, 0.6);
                border-radius: 50%;
                animation: particleFloat 8s infinite linear;
            }

            .particle-1 {
                top: 20%;
                left: 10%;
                animation-delay: 0s;
            }

            .particle-2 {
                top: 60%;
                left: 80%;
                animation-delay: 2s;
            }

            .particle-3 {
                top: 80%;
                left: 20%;
                animation-delay: 4s;
            }

            .particle-4 {
                top: 40%;
                left: 90%;
                animation-delay: 6s;
            }

            .particle-5 {
                top: 10%;
                left: 70%;
                animation-delay: 1s;
            }

            .particle-6 {
                top: 30%;
                left: 50%;
                animation-delay: 3s;
            }

            /* Sección Principal del Footer */
            .footer-main-pro {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1.5fr;
                gap: 3rem;
                padding: 4rem 0;
                position: relative;
                z-index: 5;
            }

            .footer-section-pro {
                position: relative;
            }

            /* Branding Section */
            .footer-brand-pro {
                position: relative;
            }

            .brand-header-pro {
                margin-bottom: 1.5rem;
            }

            .brand-logo-pro {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 0.5rem;
            }

            .brand-icon-pro {
                font-size: 2.2rem;
                filter: drop-shadow(0 0 10px rgba(255, 107, 0, 0.6));
            }

            .brand-name-pro {
                font-family: 'Montserrat', Arial, sans-serif;
                font-size: 2rem;
                font-weight: 900;
                color: white;
                margin: 0;
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                background-clip: text;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .brand-badge-pro {
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                color: white;
                padding: 0.2rem 0.6rem;
                border-radius: 8px;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
            }

            .brand-slogan {
                margin-bottom: 1rem;
            }

            .brand-slogan span {
                color: rgba(255, 255, 255, 0.8);
                font-style: italic;
                font-size: 0.9rem;
            }

            .brand-description-pro {
                color: rgba(255, 255, 255, 0.8);
                line-height: 1.6;
                margin-bottom: 2rem;
                font-size: 1rem;
            }

            /* Información de Contacto */
            .contact-info-pro {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .contact-item-pro {
                display: flex;
                gap: 1rem;
                align-items: flex-start;
                padding: 1rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                border: 1px solid rgba(255, 107, 0, 0.1);
                transition: all 0.3s ease;
            }

            .contact-item-pro:hover {
                background: rgba(255, 255, 255, 0.08);
                border-color: rgba(255, 107, 0, 0.3);
                transform: translateY(-2px);
            }

            .contact-icon-pro {
                width: 45px;
                height: 45px;
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.1rem;
                flex-shrink: 0;
                box-shadow: 0 6px 20px rgba(255, 107, 0, 0.3);
            }

            .contact-details-pro {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .contact-label-pro {
                color: #FF6B00;
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .contact-value-pro {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.95rem;
                line-height: 1.4;
            }

            /* Títulos de Sección */
            .footer-title-pro {
                color: white;
                font-family: 'Montserrat', Arial, sans-serif;
                font-size: 1.3rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                position: relative;
            }

            .footer-title-pro::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 0;
                width: 50px;
                height: 2px;
                background: linear-gradient(90deg, #FF6B00, #FFB366);
                border-radius: 1px;
            }

            .footer-title-icon {
                color: #FF6B00;
                font-size: 1.2rem;
            }

            /* Enlaces del Footer */
            .footer-links-pro {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .footer-links-pro li {
                margin: 0;
            }

            .footer-links-pro a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 0;
                transition: all 0.3s ease;
                border-radius: 8px;
                position: relative;
            }

            .footer-links-pro a:hover {
                color: #FF6B00;
                padding-left: 1rem;
                background: rgba(255, 107, 0, 0.1);
                transform: translateX(5px);
            }

            .footer-links-pro a i {
                color: #FF6B00;
                font-size: 0.9rem;
                width: 16px;
                flex-shrink: 0;
            }

            /* Sección de Conexión */
            .footer-connect-pro {
                background: rgba(255, 255, 255, 0.03);
                padding: 2rem;
                border-radius: 20px;
                border: 1px solid rgba(255, 107, 0, 0.1);
            }

            .connect-description-pro {
                color: rgba(255, 255, 255, 0.8);
                line-height: 1.6;
                margin-bottom: 2rem;
                font-size: 0.95rem;
            }

            /* Newsletter */
            .newsletter-section-pro {
                margin-bottom: 2rem;
            }

            .newsletter-title-pro {
                color: white;
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .newsletter-form-pro {
                margin-bottom: 1rem;
            }

            .newsletter-input-group-pro {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .input-wrapper-pro {
                position: relative;
                display: flex;
                align-items: center;
            }

            .input-icon-pro {
                position: absolute;
                left: 1rem;
                color: #FF6B00;
                font-size: 1rem;
                z-index: 2;
            }

            .newsletter-input-pro {
                width: 100%;
                padding: 1rem 1rem 1rem 3rem;
                border: 2px solid rgba(255, 107, 0, 0.2);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.05);
                color: white;
                font-size: 1rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .newsletter-input-pro:focus {
                outline: none;
                border-color: #FF6B00;
                background: rgba(255, 255, 255, 0.08);
                box-shadow: 0 0 20px rgba(255, 107, 0, 0.2);
            }

            .newsletter-input-pro::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            .newsletter-btn-pro {
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 0.9rem;
                box-shadow: 0 6px 20px rgba(255, 107, 0, 0.3);
                width: 100%;
            }

            .newsletter-btn-pro:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(255, 107, 0, 0.4);
            }

            .newsletter-benefits-pro {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .newsletter-benefits-pro small {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.8rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .newsletter-benefits-pro i {
                color: #28a745;
                font-size: 0.8rem;
            }

            /* Redes Sociales */
            .social-section-pro {
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .social-title-pro {
                color: white;
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .social-links-pro {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }

            .social-link-pro {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-size: 1.2rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .social-link-pro.facebook {
                background: linear-gradient(135deg, #1877f2, #42a5f5);
                color: white;
            }

            .social-link-pro.instagram {
                background: linear-gradient(135deg, #e1306c, #fd1d1d, #fcb045);
                color: white;
            }

            .social-link-pro.twitter {
                background: linear-gradient(135deg, #1da1f2, #0d8bd9);
                color: white;
            }

            .social-link-pro.youtube {
                background: linear-gradient(135deg, #ff0000, #cc0000);
                color: white;
            }

            .social-link-pro.tiktok {
                background: linear-gradient(135deg, #000000, #333333);
                color: white;
            }

            .social-link-pro.whatsapp {
                background: linear-gradient(135deg, #25d366, #128c7e);
                color: white;
            }

            .social-link-pro:hover {
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            }

            .social-tooltip {
                position: absolute;
                bottom: 120%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 6px;
                font-size: 0.7rem;
                white-space: nowrap;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 10;
            }

            .social-link-pro:hover .social-tooltip {
                opacity: 1;
            }

            /* CTA de Contacto */
            .contact-cta-pro {
                margin-top: 2rem;
                padding: 2rem;
                background: rgba(255, 107, 0, 0.1);
                border-radius: 15px;
                border: 1px solid rgba(255, 107, 0, 0.2);
                text-align: center;
            }

            .contact-cta-title-pro {
                color: white;
                font-size: 1.2rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .contact-cta-text-pro {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .contact-cta-btn-pro {
                display: inline-flex;
                align-items: center;
                gap: 0.75rem;
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                color: white;
                padding: 1rem 2rem;
                border-radius: 25px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
                box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3);
                border: none;
            }

            .contact-cta-btn-pro:hover {
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 12px 35px rgba(255, 107, 0, 0.4);
                color: white;
                text-decoration: none;
            }

            /* Separador */
            .footer-separator-pro {
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255, 107, 0, 0.3), transparent);
                margin: 2rem 0;
                position: relative;
                z-index: 5;
            }

            /* Badges de Confianza */
            .footer-badges-pro {
                padding: 2rem 0;
                position: relative;
                z-index: 5;
            }

            .badges-grid-pro {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .badge-item-pro {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1.5rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 15px;
                border: 1px solid rgba(255, 107, 0, 0.1);
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .badge-item-pro:hover {
                transform: translateY(-3px);
                background: rgba(255, 255, 255, 0.08);
                border-color: rgba(255, 107, 0, 0.3);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .badge-icon-pro {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.3rem;
                flex-shrink: 0;
                box-shadow: 0 6px 20px rgba(255, 107, 0, 0.3);
            }

            .badge-content-pro {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .badge-title-pro {
                color: white;
                font-weight: 700;
                font-size: 1rem;
            }

            .badge-subtitle-pro {
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.85rem;
            }

            /* Métodos de Pago */
            .payment-methods-pro {
                margin-top: 2rem;
                padding: 2rem;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 15px;
                border: 1px solid rgba(255, 107, 0, 0.1);
            }

            .payment-title-pro {
                color: white;
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .payment-icons-pro {
                display: flex;
                justify-content: center;
                gap: 2rem;
                flex-wrap: wrap;
            }

            .payment-group-pro {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
            }

            .payment-group-title {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
                font-weight: 600;
            }

            .payment-icons-grid {
                display: flex;
                gap: 1rem;
                align-items: center;
            }

            .payment-icons-grid i {
                font-size: 2rem;
                color: rgba(255, 255, 255, 0.8);
                transition: all 0.3s ease;
            }

            .payment-icons-grid i:hover {
                color: #FF6B00;
                transform: scale(1.1);
            }

            .payment-text {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                font-size: 0.9rem;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .payment-text:hover {
                background: rgba(255, 107, 0, 0.2);
                transform: scale(1.05);
            }

            /* Footer Bottom */
            .footer-bottom-pro {
                padding: 2rem 0;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                position: relative;
                z-index: 5;
            }

            .footer-bottom-content-pro {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 2rem;
            }

            .copyright-section-pro {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .copyright-main-pro p {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
                margin: 0;
            }

            .copyright-tagline-pro {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.85rem;
            }

            .heart-icon-pro {
                color: #FF6B00;
                animation: heartBeat 2s infinite;
            }

            .company-stats-pro {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .stat-item-pro {
                text-align: center;
            }

            .stat-number-pro {
                display: block;
                font-size: 1.5rem;
                font-weight: 700;
                color: #FF6B00;
                margin-bottom: 0.25rem;
            }

            .stat-label-pro {
                display: block;
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.7);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .stat-separator-pro {
                color: rgba(255, 255, 255, 0.3);
                font-size: 1.2rem;
            }

            /* Enlaces Legales */
            .legal-links-pro {
                display: flex;
                gap: 2rem;
                flex-wrap: wrap;
            }

            .legal-links-pro a {
                color: rgba(255, 255, 255, 0.6);
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .legal-links-pro a:hover {
                color: #FF6B00;
                text-decoration: underline;
            }

            /* Botón Back to Top */
            .back-to-top-pro {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #FF6B00, #FFB366);
                border: none;
                border-radius: 50%;
                color: white;
                font-size: 1.2rem;
                cursor: pointer;
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
                transform: translateY(20px);
                transition: all 0.3s ease;
                box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.25rem;
            }

            .back-to-top-pro.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .back-to-top-pro:hover {
                transform: translateY(-5px) scale(1.1);
                box-shadow: 0 15px 40px rgba(255, 107, 0, 0.4);
            }

            .back-to-top-text {
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Animaciones */
            @keyframes footerGlow {
                0%, 100% {
                    transform: translate(-50%, -50%) scale(1);
                    opacity: 0.3;
                }
                50% {
                    transform: translate(-50%, -50%) scale(1.1);
                    opacity: 0.5;
                }
            }

            @keyframes particleFloat {
                0% {
                    transform: translateY(0px) translateX(0px) scale(1);
                    opacity: 0.6;
                }
                25% {
                    transform: translateY(-20px) translateX(10px) scale(1.1);
                    opacity: 1;
                }
                50% {
                    transform: translateY(-10px) translateX(-5px) scale(0.9);
                    opacity: 0.8;
                }
                75% {
                    transform: translateY(-30px) translateX(15px) scale(1.2);
                    opacity: 1;
                }
                100% {
                    transform: translateY(0px) translateX(0px) scale(1);
                    opacity: 0.6;
                }
            }

            @keyframes heartBeat {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.2);
                }
            }

            /* Responsive Design */
            @media (max-width: 1024px) {
                .footer-main-pro {
                    grid-template-columns: 1fr 1fr;
                    gap: 2rem;
                }
                
                .footer-brand-pro {
                    grid-column: 1 / -1;
                }
                
                .contact-info-pro {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1rem;
                }
                
                .badges-grid-pro {
                    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                    gap: 1rem;
                }
                
                .payment-icons-pro {
                    flex-direction: column;
                    gap: 1.5rem;
                }
            }

            @media (max-width: 768px) {
                .footer-main-pro {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                    padding: 3rem 0;
                }
                
                .contact-info-pro {
                    grid-template-columns: 1fr;
                }
                
                .footer-bottom-content-pro {
                    flex-direction: column;
                    text-align: center;
                    gap: 1.5rem;
                }
                
                .company-stats-pro {
                    justify-content: center;
                }
                
                .legal-links-pro {
                    justify-content: center;
                    flex-direction: column;
                    gap: 1rem;
                    text-align: center;
                }
                
                .social-links-pro {
                    grid-template-columns: repeat(6, 1fr);
                    gap: 0.75rem;
                }
                
                .social-link-pro {
                    width: 45px;
                    height: 45px;
                    font-size: 1.1rem;
                }
                
                .badges-grid-pro {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }
                
                .badge-item-pro {
                    padding: 1rem;
                }
                
                .badge-icon-pro {
                    width: 40px;
                    height: 40px;
                    font-size: 1.1rem;
                }
                
                .back-to-top-pro {
                    width: 50px;
                    height: 50px;
                    bottom: 20px;
                    right: 20px;
                    font-size: 1rem;
                }
                
                .back-to-top-text {
                    display: none;
                }
            }

            @media (max-width: 480px) {
                .footer-main-pro {
                    padding: 2rem 0;
                }
                
                .brand-name-pro {
                    font-size: 1.8rem;
                }
                
                .contact-item-pro {
                    padding: 0.75rem;
                }
                
                .contact-icon-pro {
                    width: 35px;
                    height: 35px;
                    font-size: 1rem;
                }
                
                .footer-title-pro {
                    font-size: 1.1rem;
                }
                
                .footer-connect-pro {
                    padding: 1.5rem;
                }
                
                .newsletter-input-group-pro {
                    gap: 0.75rem;
                }
                
                .newsletter-btn-pro {
                    padding: 0.75rem 1.5rem;
                    font-size: 0.85rem;
                }
                
                .social-links-pro {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 1rem;
                }
                
                .contact-cta-pro {
                    padding: 1.5rem;
                }
                
                .contact-cta-btn-pro {
                    padding: 0.75rem 1.5rem;
                    font-size: 0.9rem;
                }
                
                .payment-methods-pro {
                    padding: 1.5rem;
                }
                
                .company-stats-pro {
                    flex-direction: column;
                    gap: 0.75rem;
                }
                
                .stat-separator-pro {
                    display: none;
                }
            }
    </style>
   </head>

    </main>
    
    <!-- Footer Ultra Moderno y Profesional -->
    <footer class="footer-enhanced-pro">
        <div class="footer-background-pro">
            <div class="footer-overlay-pro"></div>
            <div class="footer-pattern"></div>
            <div class="footer-glow"></div>
            <div class="footer-particles">
                <div class="particle particle-1"></div>
                <div class="particle particle-2"></div>
                <div class="particle particle-3"></div>
                <div class="particle particle-4"></div>
                <div class="particle particle-5"></div>
                <div class="particle particle-6"></div>
            </div>
        </div>
        
        <div class="container">
            <!-- Sección Principal del Footer -->
            <div class="footer-main-pro">
                <!-- Información de la Empresa -->
                <div class="footer-section-pro footer-brand-pro">
                    <div class="brand-header-pro">
                        <div class="brand-logo-pro">
                            <span class="brand-icon-pro">💪</span>
                            <h3 class="brand-name-pro">STYLOFITNESS</h3>
                            <span class="brand-badge-pro">PRO</span>
                        </div>
                        <div class="brand-slogan">
                            <span>Transformamos vidas a través del fitness</span>
                        </div>
                    </div>
                    
                    <p class="brand-description-pro">
                        La plataforma de fitness más avanzada del país, combinando tecnología de vanguardia 
                        con entrenamientos personalizados y suplementos premium para lograr tus objetivos.
                    </p>
                    
                    <!-- Información de Contacto Mejorada -->
                    <div class="contact-info-pro">
                        <div class="contact-item-pro">
                            <div class="contact-icon-pro">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details-pro">
                                <span class="contact-label-pro">Sede Principal</span>
                                <span class="contact-value-pro">Av. Principal 123, San Isidro, Lima</span>
                            </div>
                        </div>
                        
                        <div class="contact-item-pro">
                            <div class="contact-icon-pro">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-details-pro">
                                <span class="contact-label-pro">Atención al Cliente</span>
                                <span class="contact-value-pro">+51 999 888 777</span>
                            </div>
                        </div>
                        
                        <div class="contact-item-pro">
                            <div class="contact-icon-pro">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details-pro">
                                <span class="contact-label-pro">Email</span>
                                <span class="contact-value-pro">info@stylofitness.com</span>
                            </div>
                        </div>
                        
                        <div class="contact-item-pro">
                            <div class="contact-icon-pro">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-details-pro">
                                <span class="contact-label-pro">Horarios</span>
                                <span class="contact-value-pro">
                                    Lun - Vie: 5:00 AM - 11:00 PM<br>
                                    Sáb - Dom: 6:00 AM - 10:00 PM
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Enlaces Rápidos -->
                <div class="footer-section-pro">
                    <h4 class="footer-title-pro">
                        <i class="fas fa-link footer-title-icon"></i>
                        Navegación
                    </h4>
                    <ul class="footer-links-pro">
                        <li><a href="<?php echo AppHelper::getBaseUrl(); ?>"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('routines'); ?>"><i class="fas fa-dumbbell"></i> Rutinas</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('classes'); ?>"><i class="fas fa-users"></i> Clases Grupales</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('trainers'); ?>"><i class="fas fa-user-tie"></i> Entrenadores</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('memberships'); ?>"><i class="fas fa-crown"></i> Membresías</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('about'); ?>"><i class="fas fa-info-circle"></i> Nosotros</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('contact'); ?>"><i class="fas fa-envelope"></i> Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Tienda -->
                <div class="footer-section-pro">
                    <h4 class="footer-title-pro">
                        <i class="fas fa-shopping-bag footer-title-icon"></i>
                        Tienda Online
                    </h4>
                    <ul class="footer-links-pro">
                        <li><a href="<?php echo AppHelper::getBaseUrl('store'); ?>"><i class="fas fa-store"></i> Todos los Productos</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/category/proteinas'); ?>"><i class="fas fa-flask"></i> Proteínas</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/category/pre-entrenos'); ?>"><i class="fas fa-bolt"></i> Pre-entrenos</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/category/vitaminas'); ?>"><i class="fas fa-pills"></i> Vitaminas</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/category/accesorios'); ?>"><i class="fas fa-tools"></i> Accesorios</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/category/ropa'); ?>"><i class="fas fa-tshirt"></i> Ropa Deportiva</a></li>
                        <li><a href="<?php echo AppHelper::getBaseUrl('store/offers'); ?>"><i class="fas fa-fire"></i> Ofertas</a></li>
                    </ul>
                </div>
                
                <!-- Redes Sociales y Newsletter -->
                <div class="footer-section-pro footer-connect-pro">
                    <h4 class="footer-title-pro">
                        <i class="fas fa-wifi footer-title-icon"></i>
                        Mantente Conectado
                    </h4>
                    <p class="connect-description-pro">
                        Únete a nuestra comunidad y recibe consejos exclusivos, rutinas personalizadas 
                        y ofertas especiales directamente en tu email.
                    </p>
                    
                    <!-- Newsletter -->
                    <div class="newsletter-section-pro">
                        <h5 class="newsletter-title-pro">Newsletter Fitness</h5>
                        <form class="newsletter-form-pro" id="newsletter-form">
                            <div class="newsletter-input-group-pro">
                                <div class="input-wrapper-pro">
                                    <i class="fas fa-envelope input-icon-pro"></i>
                                    <input type="email" 
                                           placeholder="Tu email aquí..." 
                                           required 
                                           class="newsletter-input-pro"
                                           name="email">
                                </div>
                                <button type="submit" class="newsletter-btn-pro">
                                    <i class="fas fa-paper-plane"></i>
                                    Suscribirse
                                </button>
                            </div>
                            <div class="newsletter-benefits-pro">
                                <small><i class="fas fa-check-circle"></i> Rutinas exclusivas</small>
                                <small><i class="fas fa-check-circle"></i> Ofertas especiales</small>
                                <small><i class="fas fa-check-circle"></i> Consejos de expertos</small>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Redes Sociales -->
                    <div class="social-section-pro">
                        <h5 class="social-title-pro">Síguenos</h5>
                        <div class="social-links-pro">
                            <a href="https://facebook.com/stylofitness" target="_blank" rel="noopener" 
                               class="social-link-pro facebook" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                                <span class="social-tooltip">Facebook</span>
                            </a>
                            <a href="https://instagram.com/stylofitness" target="_blank" rel="noopener" 
                               class="social-link-pro instagram" title="Instagram">
                                <i class="fab fa-instagram"></i>
                                <span class="social-tooltip">Instagram</span>
                            </a>
                            <a href="https://twitter.com/stylofitness" target="_blank" rel="noopener" 
                               class="social-link-pro twitter" title="Twitter">
                                <i class="fab fa-twitter"></i>
                                <span class="social-tooltip">Twitter</span>
                            </a>
                            <a href="https://youtube.com/stylofitness" target="_blank" rel="noopener" 
                               class="social-link-pro youtube" title="YouTube">
                                <i class="fab fa-youtube"></i>
                                <span class="social-tooltip">YouTube</span>
                            </a>
                            <a href="https://tiktok.com/@stylofitness" target="_blank" rel="noopener" 
                               class="social-link-pro tiktok" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                                <span class="social-tooltip">TikTok</span>
                            </a>
                            <a href="https://wa.me/51999888777" target="_blank" rel="noopener" 
                               class="social-link-pro whatsapp" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                                <span class="social-tooltip">WhatsApp</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- CTA de Contacto -->
                    <div class="contact-cta-pro">
                        <h5 class="contact-cta-title-pro">¿Necesitas ayuda?</h5>
                        <p class="contact-cta-text-pro">Nuestros expertos están listos para asesorarte</p>
                        <a href="tel:+51999888777" class="contact-cta-btn-pro">
                            <i class="fas fa-phone"></i>
                            <span>Llamar Ahora</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Separador Decorativo -->
            <div class="footer-separator-pro"></div>
            
            <!-- Badges de Confianza -->
            <div class="footer-badges-pro">
                <div class="badges-grid-pro">
                    <div class="badge-item-pro">
                        <div class="badge-icon-pro">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="badge-content-pro">
                            <span class="badge-title-pro">Soporte 24/7</span>
                            <span class="badge-subtitle-pro">Asistencia Premium</span>
                        </div>
                    </div>
                    
                    <div class="badge-item-pro">
                        <div class="badge-icon-pro">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="badge-content-pro">
                            <span class="badge-title-pro">Compra Segura</span>
                            <span class="badge-subtitle-pro">SSL Certificado</span>
                        </div>
                    </div>
                    
                    <div class="badge-item-pro">
                        <div class="badge-icon-pro">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="badge-content-pro">
                            <span class="badge-title-pro">Envío Gratis</span>
                            <span class="badge-subtitle-pro">Compras +S/150</span>
                        </div>
                    </div>
                    
                    <div class="badge-item-pro">
                        <div class="badge-icon-pro">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="badge-content-pro">
                            <span class="badge-title-pro">Calidad Premium</span>
                            <span class="badge-subtitle-pro">Productos Certificados</span>
                        </div>
                    </div>
                    
                    <div class="badge-item-pro">
                        <div class="badge-icon-pro">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <div class="badge-content-pro">
                            <span class="badge-title-pro">30 Días</span>
                            <span class="badge-subtitle-pro">Garantía Devolución</span>
                        </div>
                    </div>
                </div>
                
                <!-- Métodos de Pago -->
                <div class="payment-methods-pro">
                    <h5 class="payment-title-pro">Métodos de Pago Seguros</h5>
                    <div class="payment-icons-pro">
                        <div class="payment-group-pro">
                            <span class="payment-group-title">Tarjetas:</span>
                            <div class="payment-icons-grid">
                                <i class="fab fa-cc-visa" title="Visa"></i>
                                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                                <i class="fab fa-cc-paypal" title="PayPal"></i>
                            </div>
                        </div>
                        <div class="payment-group-pro">
                            <span class="payment-group-title">Billeteras:</span>
                            <div class="payment-icons-grid">
                                <span class="payment-text">Yape</span>
                                <span class="payment-text">Plin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Separador -->
            <div class="footer-separator-pro"></div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom-pro">
                <div class="footer-bottom-content-pro">
                    <!-- Copyright -->
                    <div class="copyright-section-pro">
                        <div class="copyright-main-pro">
                            <p>&copy; <?php echo date('Y'); ?> <strong>STYLOFITNESS</strong>. Todos los derechos reservados.</p>
                            <p class="copyright-tagline-pro">
                                Desarrollado con <span class="heart-icon-pro">💪</span> para transformar vidas a través del fitness.
                            </p>
                        </div>
                        
                        <!-- Estadísticas -->
                        <div class="company-stats-pro">
                            <div class="stat-item-pro">
                                <span class="stat-number-pro">10K+</span>
                                <span class="stat-label-pro">Clientes</span>
                            </div>
                            <div class="stat-separator-pro">|</div>
                            <div class="stat-item-pro">
                                <span class="stat-number-pro">15</span>
                                <span class="stat-label-pro">Sedes</span>
                            </div>
                            <div class="stat-separator-pro">|</div>
                            <div class="stat-item-pro">
                                <span class="stat-number-pro">98%</span>
                                <span class="stat-label-pro">Satisfacción</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Enlaces Legales -->
                    <div class="legal-links-pro">
                        <a href="<?php echo AppHelper::getBaseUrl('privacy'); ?>">Política de Privacidad</a>
                        <a href="<?php echo AppHelper::getBaseUrl('terms'); ?>">Términos y Condiciones</a>
                        <a href="<?php echo AppHelper::getBaseUrl('refund'); ?>">Política de Devoluciones</a>
                        <a href="<?php echo AppHelper::getBaseUrl('shipping'); ?>">Información de Envío</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Botón Back to Top Mejorado -->
    <button class="back-to-top-pro" id="back-to-top" title="Ir arriba">
        <i class="fas fa-chevron-up"></i>
        <span class="back-to-top-text">Subir</span>
    </button>
    
    <!-- Scripts JavaScript -->
    
    <!-- jQuery (si es necesario para algún plugin) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" defer></script>
    
    <!-- Swiper para carruseles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js" defer></script>
    
    <!-- AOS (Animate On Scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>
    
    <!-- JavaScript principal -->
    <script src="<?php echo AppHelper::asset('js/app.js'); ?>" defer></script>
    
    <!-- Scripts adicionales según la página -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo AppHelper::asset("js/{$js}"); ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Scripts inline si es necesario -->
    <?php if (isset($inlineJS)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php echo $inlineJS; ?>
            });
        </script>
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    
    <!-- Facebook Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'FB_PIXEL_ID'); 
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" 
             src="https://www.facebook.com/tr?id=FB_PIXEL_ID&ev=PageView&noscript=1"/>
    </noscript>
    
    <!-- Schema.org datos estructurados específicos de la página -->
    <?php if (isset($structuredData)): ?>
        <script type="application/ld+json">
            <?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endif; ?>
    
    <!-- Inicialización final -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar loading screen
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                setTimeout(() => {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 300);
                }, 1000);
            }
            
            // Inicializar AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100
                });
            }
            
            // Back to top button
            const backToTop = document.getElementById('back-to-top');
            if (backToTop) {
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > 300) {
                        backToTop.classList.add('show');
                    } else {
                        backToTop.classList.remove('show');
                    }
                });
                
                backToTop.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Newsletter form
            const newsletterForm = document.getElementById('newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = this.querySelector('input[name="email"]').value;
                    
                    if (email) {
                        // Simular envío
                        const btn = this.querySelector('.newsletter-btn-pro');
                        const originalText = btn.innerHTML;
                        
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                        btn.disabled = true;
                        
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fas fa-check"></i> ¡Suscrito!';
                            btn.style.background = '#28a745';
                            
                            setTimeout(() => {
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                btn.style.background = '';
                                this.reset();
                            }, 2000);
                        }, 1500);
                    }
                });
            }
            
            // Flash messages auto-hide
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(message => {
                const closeBtn = message.querySelector('.flash-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        message.style.opacity = '0';
                        setTimeout(() => {
                            message.remove();
                        }, 300);
                    });
                }
                
                // Auto-hide después de 5 segundos
                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.opacity = '0';
                        setTimeout(() => {
                            if (message.parentNode) {
                                message.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
            
            // Efectos adicionales para el footer
            const footerLinks = document.querySelectorAll('.footer-links-pro a');
            footerLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
            
            // Efecto hover para badges
            const badges = document.querySelectorAll('.badge-item-pro');
            badges.forEach(badge => {
                badge.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                badge.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Animación de partículas en footer
            const particles = document.querySelectorAll('.footer-particles .particle');
            particles.forEach((particle, index) => {
                particle.style.animationDelay = `${index * 1.5}s`;
                particle.style.animationDuration = `${6 + index}s`;
            });
        });
        
        // Service Worker para PWA (opcional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</body>
</html>
