import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def enviar_email_encomenda(email_destinatario, nome_morador, descricao, data_recebimento):
    email_sender = 'hudsonborges64@gmail.com'
    senha_sender = 'cdqb bern jixp djui'

    assunto = "Nova encomenda recebida!"
    corpo_email = f"""
    <html>
    <body>
        <div style='font-family: Arial; background: #f8fafc; padding: 20px; border-radius: 10px; color: #3498db;'>
            <h2>Olá, {nome_morador}!</h2>
            <p>Sua encomenda chegou:</p>
            <ul>
                <li><b>Descrição:</b> {descricao}</li>
                <li><b>Data de recebimento:</b> {data_recebimento}</li>
            </ul>
            <p>Por favor, retire sua encomenda na portaria.</p>
            <br>
            <div style='color: #3498db;'>Equipe ShieldTech</div>
        </div>
    </body>
    </html>
    """

    mensagem = MIMEMultipart()
    mensagem['From'] = email_sender
    mensagem['To'] = email_destinatario
    mensagem['Subject'] = assunto
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
        print("Erro ao enviar email:", e)
        return False
