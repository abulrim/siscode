<?php
class YuiCompressor {
    
    
    public static function compress($fileName,$minFileName){
        $absolutePath=__FILE__;
        $absolutePath=str_replace('YuiCompressor.php','',$absolutePath);
        $compressorPath=$absolutePath.'compressor/yuicompressor-2.4.7.jar';
        
        if(file_exists($minFileName)){
            unlink($minFileName);
        }
        
        $command='java -jar '.$compressorPath.' '.$fileName.' -o '.$minFileName.' --charset utf-8';
        $result=exec($command);
        
        return $fileName;
    }
}
?>
