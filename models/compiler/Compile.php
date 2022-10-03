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
        $filePath = __DIR__ ."/$random.$this->language";
        $programFile = fopen($filePath, 'w');
            
        try{ 
            fwrite($programFile, $this->code);
            fclose($programFile);
            // chmod($filePath, 0777);
        }catch (Exception $e){
            echo $e;
        }

        
  
        if($this->language == 'php'){
            $output = shell_exec("php $filePath 2>$1");
            echo $output;
        }

        
        if($this->language == 'python'){
            $output = shell_exec("python3 $filePath 2>$1");
            echo $output;
        }

        
        if($this->language == 'js'){
            $output = shell_exec("node $filePath 2>$1");
            echo $output;
        }

        if($this->language == 'c'){
            $outputExe = "$random.exe";
            shell_exec("gcc $filePath -o $outputExe");
            $output = shell_exec(__DIR__."//$outputExe");
            echo $output;
        }

        if($this->language == 'cpp'){
            $outputExe = "$random.exe";
            shell_exec("g++ $filePath -o $outputExe");
            $output = shell_exec(__DIR__."//$outputExe");
            echo $output;
        }
    }
}