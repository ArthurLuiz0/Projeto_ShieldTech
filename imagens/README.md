# Pasta de Imagens - ShieldTech

Esta pasta armazena as fotos enviadas pelos usuários do sistema.

## Estrutura

- `moradores/` - Fotos dos moradores
- `visitantes/` - Fotos dos visitantes

## Características

- **Tamanho máximo:** 5MB por arquivo
- **Formatos aceitos:** JPG, JPEG, PNG, GIF, WebP
- **Redimensionamento:** Automático para máximo 800x800px
- **Segurança:** Configurada via .htaccess

## Funcionalidades

- Upload automático com validação
- Redimensionamento inteligente
- Nomes únicos para evitar conflitos
- Remoção automática de fotos antigas ao editar
- Preview em tempo real

## Segurança

- Bloqueio de execução de scripts
- Validação de tipo de arquivo
- Verificação de imagem real
- Proteção contra listagem de diretório

## Manutenção

Para limpar fotos antigas não utilizadas, execute uma verificação periódica no banco de dados e remova arquivos órfãos.