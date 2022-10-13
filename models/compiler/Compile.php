<?php
class Compile{

    private string $language;
    private string $code;

    public function __construct(string $language, string $code){
        
        $this->language = $language;
        $this->code = $code;
    }

    public function execute(){

        $random = substr(md5(mt_rand()), 0, 7);
        $filePath  = "./temp/$random.$this->language";
        // $filePath = __DIR__ ."/$random.$this->language";
        $programFile = fopen($filePath, 'w');
            
        try{ 
            fwrite($programFile, $this->code);
            fclose($programFile);
            chmod($filePath,0777);
        }catch (Exception $e){
            echo $e;
        }

        
        if($this->language == 'php'){
            $output = shell_exec("php $filePath 2>&1");
            echo $output;
        }

        
        if($this->language == 'py'){
            $output = shell_exec("python3 $filePath 2>&1");
            echo $output;
        }

        
        if($this->language == 'js'){
            $output = shell_exec("node $filePath 2>&1");
            echo $output;
        }

        if($this->language == 'c'){
            $outputExe = "temp/$random.exe";
            shell_exec("gcc $filePath -o $outputExe");
            chmod($outputExe,0777);
            $output = shell_exec("$outputExe");
            unlink($outputExe);
            echo $output;
        }

        if($this->language == 'cpp'){
            $outputExe = "temp/$random.exe";
            shell_exec("g++ $filePath -o $outputExe");
            chmod($outputExe,0777);
            $output = shell_exec("$outputExe");
            unlink($outputExe);
            echo $output;
        }

        unlink($filePath);
    }
}



