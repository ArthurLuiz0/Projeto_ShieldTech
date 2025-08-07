<?php
/**
 * Classe para gerenciamento de upload de fotos
 * Sistema ShieldTech
 */

class PhotoUpload {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct($type = 'moradores') {
        $this->uploadDir = __DIR__ . "/../imagens/$type/";
        
        // Criar diretório se não existir
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Faz upload de uma foto
     * @param array $file Arquivo $_FILES
     * @param string $prefix Prefixo para o nome do arquivo
     * @return array Resultado do upload
     */
    public function uploadPhoto($file, $prefix = '') {
        try {
            // Verificar se houve erro no upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return $this->getErrorResult('Erro no upload: ' . $this->getUploadErrorMessage($file['error']));
            }
            
            // Verificar tamanho do arquivo
            if ($file['size'] > $this->maxFileSize) {
                return $this->getErrorResult('Arquivo muito grande. Máximo permitido: 5MB');
            }
            
            // Verificar tipo do arquivo
            if (!in_array($file['type'], $this->allowedTypes)) {
                return $this->getErrorResult('Tipo de arquivo não permitido. Use: JPG, PNG, GIF ou WebP');
            }
            
            // Verificar se é realmente uma imagem
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return $this->getErrorResult('Arquivo não é uma imagem válida');
            }
            
            // Gerar nome único para o arquivo
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;
            
            // Mover arquivo para o diretório de destino
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Redimensionar imagem se necessário
                $this->resizeImage($filepath, 800, 800);
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'url' => $this->getPhotoUrl($filename),
                    'message' => 'Foto enviada com sucesso!'
                ];
            } else {
                return $this->getErrorResult('Erro ao salvar arquivo no servidor');
            }
            
        } catch (Exception $e) {
            return $this->getErrorResult('Erro interno: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove uma foto do servidor
     * @param string $filename Nome do arquivo
     * @return bool
     */
    public function deletePhoto($filename) {
        if (empty($filename)) return true;
        
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return true;
    }
    
    /**
     * Redimensiona uma imagem mantendo proporção
     * @param string $filepath Caminho do arquivo
     * @param int $maxWidth Largura máxima
     * @param int $maxHeight Altura máxima
     */
    private function resizeImage($filepath, $maxWidth, $maxHeight) {
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) return;
        
        list($width, $height, $type) = $imageInfo;
        
        // Verificar se precisa redimensionar
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }
        
        // Calcular novas dimensões mantendo proporção
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        // Criar imagem baseada no tipo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filepath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filepath);
                break;
            default:
                return;
        }
        
        if (!$source) return;
        
        // Criar nova imagem redimensionada
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preservar transparência para PNG e GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensionar
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Salvar imagem redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filepath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filepath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filepath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $filepath, 85);
                break;
        }
        
        // Liberar memória
        imagedestroy($source);
        imagedestroy($destination);
    }
    
    /**
     * Retorna URL da foto
     * @param string $filename Nome do arquivo
     * @return string
     */
    public function getPhotoUrl($filename) {
        return '../imagens/' . basename($this->uploadDir) . '/' . $filename;
    }
    
    /**
     * Retorna resultado de erro padronizado
     * @param string $message Mensagem de erro
     * @return array
     */
    private function getErrorResult($message) {
        return [
            'success' => false,
            'message' => $message,
            'filename' => null,
            'url' => null
        ];
    }
    
    /**
     * Converte código de erro do upload em mensagem
     * @param int $code Código de erro
     * @return string
     */
    private function getUploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Arquivo muito grande';
            case UPLOAD_ERR_PARTIAL:
                return 'Upload incompleto';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo selecionado';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Diretório temporário não encontrado';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Erro de escrita no disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bloqueado por extensão';
            default:
                return 'Erro desconhecido';
        }
    }
}
?>