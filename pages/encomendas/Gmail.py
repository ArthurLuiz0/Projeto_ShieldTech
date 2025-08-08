import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def enviar_email(email_destinatario, descricao):
    #Configurações do remetente
    email_sender = 'hudsonborges64@gmail.com'
    senha_sender = 'cdqb bern jixp djui'
    
    #Criando mensagem
    mensagem = MIMEMultipart()
    mensagem['From'] = email_sender
    mensagem['To'] = email_destinatario
    mensagem['Subject'] = 'Mensagem Especial'

    corpo_email = f"""
    <html>
    <head>
        <style>
            body {{
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #f8fafc;
                color: #3498db;
                padding: 0;
                margin: 0;
                line-height: 1.6;
            }}
            .container {{
                background: #ffffff;
                border: 2px solid #3498db;
                border-radius: 15px;
                max-width: 600px;
                margin: 30px auto;
                padding: 0;
                box-shadow: 0 10px 30px rgba(52, 152, 219, 0.15);
                overflow: hidden;
            }}
            .header {{
                background: linear-gradient(135deg, #3498db 0%, #5dade2 100%);
                color: #ffffff;
                padding: 25px;
                text-align: center;
            }}
            .header h1 {{
                margin: 0;
                font-size: 28px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }}
            .header .subtitle {{
                margin: 10px 0 0 0;
                font-size: 16px;
                opacity: 0.9;
            }}
            .content {{
                padding: 30px;
            }}
            .greeting {{
                font-size: 18px;
                color: #3498db;
                margin-bottom: 20px;
                font-weight: 500;
            }}
            .success-message {{
                background: #d4edda;
                color: #155724;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #28a745;
                margin: 20px 0;
                font-weight: 500;
            }}
            .descricao {{
                background: #e8f4fd;
                color: #3498db;
                border-radius: 8px;
                padding: 20px;
                margin: 15px 0;
                font-size: 16px;
                border-left: 4px solid #3498db;
            }}
            .descricao h3 {{
                margin: 0 0 15px 0;
                color: #3498db;
                font-size: 18px;
                display: flex;
                align-items: center;
                gap: 8px;
            }}
            .info-item {{
                margin: 8px 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }}
            .info-item strong {{
                color: #3498db;
                min-width: 100px;
            }}
            .important-notes {{
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
            }}
            .important-notes h3 {{
                margin: 0 0 15px 0;
                color: #856404;
                display: flex;
                align-items: center;
                gap: 8px;
            }}
            .important-notes ul {{
                margin: 0;
                padding-left: 20px;
                color: #856404;
            }}
            .important-notes li {{
                margin: 5px 0;
            }}
            .footer {{
                background: #f8f9fa;
                color: #6c757d;
                font-size: 14px;
                padding: 20px;
                text-align: center;
                border-top: 1px solid #e9ecef;
            }}
            .footer .company {{
                font-weight: bold;
                color: #3498db;
                margin-bottom: 5px;
            }}
            .shield-icon {{
                color: #3498db;
                font-size: 24px;
            }}
        </style>
    </head>
    <body>
        <div class="container">
            <div class='header'>
                <h1>
                    <span class='shield-icon'>🛡️</span>
                    ShieldTech
                </h1>
                <div class='subtitle'>Sistema de Controle de Acesso Inteligente</div>
            </div>
            
            <div class='content'>
                <div class='greeting'>Olá! Tudo bem?</div>
                
                <div class='success-message'>
                    ✅ <strong>Sua encomenda foi recebida com sucesso!</strong>
                </div>
                
    
                
                <div style='background: #e8f5e8; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;'>
                    <strong>🎉 Aproveite sua reserva!</strong><br>
                    Agradecemos por utilizar nossos serviços. Sua satisfação é nossa prioridade.
                </div>
            </div>
            
            <div class="descricao">
                <b>Sua mensagem:</b><br>
                {descricao if descricao.strip() else "Nenhuma descrição fornecida."}
            </div>
            <div class="resposta">
                {f"Recebemos sua encomenda: \"{descricao.strip()}\".<br> Sua encomenda chegou!,por favor, venha buscar." if descricao.strip() else "Sua encomenda chegou ,por favor, venha buscar."}
            </div>
            <div class="footer">
                <div class='company'>ShieldTech - Sistema de Controle de Acesso</div>
                <div>Este é um email automático, não responda esta mensagem.</div>
                <div>© 2025 ShieldTech. Todos os direitos reservados.</div>
                <div style='margin-top: 10px; color: #3498db;'>
                    📧 contato@shieldtech.com | 📞 (11) 1234-5678
                </div>
            </div>
        </div>
    </body>
    </html>
    """

    mensagem.attach(MIMEText(corpo_email, 'html'))

    try:
        servidor = smtplib.SMTP('smtp.gmail.com', 587)
        servidor.starttls()
        servidor.login(email_sender, senha_sender)
        texto = mensagem.as_string()
        servidor.sendmail(email_sender, email_destinatario, texto)
        servidor.quit()
        print("Email enviado com sucesso para", email_destinatario)
        return True
    except Exception as e:
        print("Erro ao enviar email:", str(e))
        return False