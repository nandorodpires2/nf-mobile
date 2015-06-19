<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Date
 *
 * @author Fernando Rodrigues
 */
class Controller_Helper_Date extends Zend_Controller_Action_Helper_Abstract {
    
    public static function getDateDateNow($format = null) {
        
        $zendDate = new Zend_Date();
        
        if ($format) {
            
            if (!Controller_Helper_Date::validFormatDate($format)) {
                throw new Zend_Controller_Action_Exception("Formato de data inválido!");
            }
            
            return $zendDate->get($format);
        }
        
        return $zendDate->get(Zend_Date::DATE_FULL);
        
    }
    
    public static function getDateTimeDateNow($format = null) {
        
        $zendDate = new Zend_Date();
        
        if ($format) {
            
            if (!Controller_Helper_Date::validFormatDate($format)) {
                throw new Zend_Controller_Action_Exception("Formato de data inválido!");
            }
            
            return $zendDate->get($format);
        }
        
        return $zendDate->get(Zend_Date::DATETIME_FULL);
        
    }
    
    protected static function validFormatDate($format) {
        
        if (in_array($format, Controller_Helper_Date::getValidFormatsDates())) {
            return true;
        } 
        return false;
        
    }
    
    protected static function getValidFormatsDates() {
        
        return array(
            Zend_Date::ATOM,
            Zend_Date::COOKIE,
            Zend_Date::DATES,
            Zend_Date::DATETIME,
            Zend_Date::DATETIME_FULL,
            Zend_Date::DATETIME_LONG,
            Zend_Date::DATETIME_MEDIUM,
            Zend_Date::DATETIME_SHORT,
            Zend_Date::DATE_FULL,
            Zend_Date::DATE_LONG,
            Zend_Date::DATE_MEDIUM,
            Zend_Date::DATE_SHORT,
            Zend_Date::DAY,
            Zend_Date::DAYLIGHT,
            Zend_Date::DAY_OF_YEAR,
            Zend_Date::DAY_SHORT,
            Zend_Date::DAY_SUFFIX,
        );
        
    }
    
    public static function getDatetimeNowDb() {
        $zendDate = new Zend_Date();
        return $zendDate->toString('yyyy-MM-dd H:m:s');        
    }
    
    public static function getDateNowDb() {
        $zendDate = new Zend_Date();
        return $zendDate->toString('yyyy-MM-dd');        
    }
    
    public static function getDateDb($date) {          
        $datePart = explode("/", $date);
        return $datePart[2] . '-' . $datePart[1] . '-' . $datePart[0];
    }
    
    public static function getDateViewComplete($date) {
        $zendDate = new Zend_Date($date);
        return $zendDate->get(Zend_Date::DATE_FULL);
    }
    
    public static function getDataEncerramentoPlano($data_aderido, $id_plano) {
        
        $modelPlano = new Model_Plano();
        $plano = $modelPlano->fetchRow("id_plano = {$id_plano} and ativo_plano = 1");
        
        // caso o plano nao tenha data de expiracao
        if (!$plano->tempo_plano) {
            return null;
        }
        
        if ($data_aderido == null) {
            $zendDate = new Zend_Date();        
        } else {        
            $zendDate = new Zend_Date($data_aderido);        
        }
        
        if ($plano->unidade_tempo_plano == 'ano') {
            $zendDate->addYear($plano->tempo_plano);
        } elseif ($plano->unidade_tempo_plano == 'meses') {
            $zendDate->addMonth($plano->tempo_plano);
        } elseif ($plano->unidade_tempo_plano == 'dias') {
            $zendDate->addDay($plano->tempo_plano);
        }                           
                
        return $zendDate->toString('yyyy-MM-dd HH:mm:ss');
        
    }
    
    public static function isLater($data) {
        
        $zendDate = new Zend_Date($data);
        
        if ($zendDate->isLater(Zend_Date::now())) {
            return true;
        }
        
        return false;
        
    }
    
    public static function getNameMonth($mes) {
                
        $zendDate = new Zend_Date();
        $zendDate->setMonth($mes);
        
        return $zendDate->get(Zend_Date::MONTH_NAME);        
        
    }
    
}
