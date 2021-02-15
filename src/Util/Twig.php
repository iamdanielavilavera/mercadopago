<?php
namespace App\Util;


class Twig
{
    protected $loader;
    protected $environment;

    public function __construct($path, $settings = []){
        $this->loader = new \Twig\Loader\FilesystemLoader($path);
        $this->environment = new \Twig\Environment($this->loader, $settings);
        $this->environment->addExtension(new \Twig\Extension\DebugExtension());
        $lexer = new \Twig\Lexer($this->environment, array(
            'tag_comment'   => array('[#', '#]'),
            'tag_block'     => array('[%', '%]'),
            'tag_variable'  => array('[[', ']]'),
            'interpolation' => array('#[', ']'),
        ));
        $this->environment->setLexer($lexer);
    }

    public function fetch($template, $data = []){
        return $this->environment->render($template, $data);
    }

    public function render($response, $template, $data = []){
        $response->getBody()->write($this->fetch($template, $data));
        return $response;
    }
}
