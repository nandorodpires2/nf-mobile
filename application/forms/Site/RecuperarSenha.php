<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RecuperarSenha
 *
 * @author Fernando Rodrigues
 */
class Form_Site_RecuperarSenha extends Zend_Form {
    
    public function init() {
        
        // email
        $this->addElement('text', 'email', array(
            'label' => 'Digite seu e-mail',
            'required' => true,
            'class' => 'form-control'
        ));
        
        /*
        $captchaImage = new Zend_Captcha_Image();
        $captchaImage->setExpiration(300)
                ->setWordlen(8)
                ->setWidth(255)
                ->setHeight(60)
                ->setImgUrl("/public/views/images/captcha/")
                ->setImgDir("./views/images/captcha/")
                ->setFont(PUBLIC_PATH . "/views/images/captcha/DIN-Bold.otf");
                
        $this->addElement('captcha', 'captcha', array(
            'class' => 'form-control',
            'placeholder' => 'Digite os caracteres da imagem',
            'captcha' => $captchaImage                         
        ));
        */
        //submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Enviar',
            'class' => 'btn btn-submit navbar-right'
        ));
        
    }
    
}
