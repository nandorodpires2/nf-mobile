<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Movimentacao
 *
 * @author Realter
 */
class Model_Movimentacao extends Zend_Db_Table {

    protected $_name = 'movimentacao';
    
    protected $_primary = 'id_movimentacao';
    
    /**
     * 
     * Retorna as que tiveram movimentacao de acordo com o mes
     * 
     * @param type $mes
     * @return type
     */
    public function getDatasMes($ano, $mes, $id_usuario, $conta = null) {
        $select = $this->select()
                ->from($this->_name, array(
                    'data_movimentacao',
                    'dia' => 'day(data_movimentacao)',
                    'id_usuario'
                ))
                ->where("year(data_movimentacao) = ?", $ano)
                ->where("month(data_movimentacao) = ?", $mes)                
                ->where("id_usuario = ?", $id_usuario)                
                ->group("data_movimentacao");
        
        // caso seja filtrado por conta
        if ($conta) {
            $select->where("id_conta = ?", $conta);
        }
                
        return $this->fetchAll($select);
        
    }
    
    /**
     *  busca os dados de uma movimentacao
     */
    public function getDadosMovimentacao($idMovimentacao, $id_usuario) {
        
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    '*'
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('tpm' => 'tipo_movimentacao'), 'mov.id_tipo_movimentacao = tpm.id_tipo_movimentacao', array(
                    'tpm.id_tipo_movimentacao',
                    'tpm.tipo_movimentacao'
                ))
                ->joinLeft(array('cat' => 'categoria'), 'mov.id_categoria = cat.id_categoria', array(
                    'cat.descricao_categoria'
                ))
                ->where("mov.id_usuario = ?", $id_usuario)
                ->where("mov.id_movimentacao = ?", $idMovimentacao);
        
        return $this->fetchRow($select);
        
    }
    
    /**
     * retorna o total de despesa do mes
     */
    public function getTotalDespesaMesAtual($id_usuario) {
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    'total_despesa' => 'sum(mov.valor_movimentacao)'
                ))
                ->setIntegrityCheck(false)
                ->where("mov.id_usuario = ?", $id_usuario)
                ->where("mov.id_tipo_movimentacao in (2,3)")
                ->where("mov.realizado = 1")
                ->where("month(mov.data_movimentacao) = month(now())")
                ->where("year(mov.data_movimentacao) = year(now())");
        
        return $this->fetchRow($select);
    }

    /**
     * retorna o ultimo id
     */
    public function lastInsertId() {
        $select = $this->select()
                ->from($this->_name, array(
                    'last_id' => 'last_insert_id(id_movimentacao)'
                ))
                ->order("id_movimentacao desc")
                ->limit(1);
        
        $query = $this->fetchRow($select);
        
        return (int)$query->last_id;
    }
    
    /**
     * retorna as movimentacoes pendentes
     */
    public function getPendencias($id_usuario) {
        
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    '*'
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('tmv' => 'tipo_movimentacao'), 'mov.id_tipo_movimentacao = tmv.id_tipo_movimentacao', array(
                    'tipo_movimentacao'
                ))
                ->joinInner(array('ct' => 'conta'), 'mov.id_conta = ct.id_conta', array('*'))
                ->joinInner(array('cat' => 'categoria'), 'mov.id_categoria = cat.id_categoria', array('*'))
                ->where("realizado = ?", 0)
                ->where("mov.id_tipo_movimentacao <> ?", 3)
                ->where("mov.id_usuario = ?", $id_usuario)
                ->where("mov.data_movimentacao < date_sub(now(), interval 1 day)")
                ->order("mov.data_movimentacao asc");
        
        return $this->fetchAll($select);
        
    }
    
    public function getProximasReceitas($id_usuario) {
        $select = $this->select()
                ->from($this->_name, array(
                    '*'
                ))
                ->where("realizado = ?", 0)
                ->where("id_usuario = ?", $id_usuario)
                ->where("id_tipo_movimentacao = ?", 1)
                ->where("data_movimentacao >= now()")
                ->order("data_movimentacao asc")
                ->limit(3);
                
        return $this->fetchAll($select);
    }
    
    public function getProximasDespesas($id_usuario) {
        $select = $this->select()
                ->from($this->_name, array(
                    '*'
                ))
                ->where("realizado = ?", 0)
                ->where("id_usuario = ?", $id_usuario)
                ->where("id_tipo_movimentacao = ?", 2)
                ->where("data_movimentacao >= now()")
                ->order("data_movimentacao asc")
                ->limit(5);
                
        return $this->fetchAll($select);
    }
    
    /**
     * retornao o total previsto para o mes da categoria
     */
    public function getTotalPrevistoCategoriaMes($id_categoria, $id_usuario) {
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    'total_categoria' => 'sum(mov.valor_movimentacao)'
                ))
                ->setIntegrityCheck(false)
                ->where("mov.id_usuario = ?", $id_usuario)
                ->where("mov.id_categoria = ?", $id_categoria)
                ->where("mov.id_tipo_movimentacao in (2,3)")                
                ->where("month(mov.data_movimentacao) = month(now())")
                ->where("year(mov.data_movimentacao) = year(now())");
        
        return $this->fetchRow($select);
    }
    
    /**
     * retornao o total previsto para o mes da categoria
     */
    public function getTotalRealizadoCategoriaMes($id_categoria, $id_usuario) {
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    'total_categoria' => 'sum(mov.valor_movimentacao)'
                ))
                ->setIntegrityCheck(false)
                ->where("mov.id_usuario = ?", $id_usuario)
                ->where("mov.id_categoria = ?", $id_categoria)
                ->where("mov.id_tipo_movimentacao in (2,3)")                
                ->where("mov.realizado = ?", 1)
                ->where("month(mov.data_movimentacao) = month(now())")
                ->where("year(mov.data_movimentacao) = year(now())");
        
        return $this->fetchRow($select);
    }
    
    /**
     * get media movimentacao categoria
     */
    public function getMediaMovimentacaoCategoria($id_categoria, $id_usuario) {
        
        $select = $this->select()
                ->from(array('mov' => $this->_name), array(
                    'mes' => 'month(mov.data_movimentacao)',
                    'ano' => 'year(mov.data_movimentacao)',
                    'total' => 'sum(mov.valor_movimentacao)'
                ))
                ->where("id_categoria = ?", $id_categoria)
                ->where("id_usuario = ?", $id_usuario)
                ->where("mov.id_tipo_movimentacao in (2,3)")
                ->group("month(mov.data_movimentacao)");
        
        return $this->fetchAll($select);			
        
    }
    
    /**
     * retorna se existe movimentacao pai para movimentacoes recorrentes
     */
    public function getIdMovimentacaoPai($id_movimentacao) {
        $movimentacao = $this->fetchRow("id_movimentacao = {$id_movimentacao}");
        if ($movimentacao->id_movimentacao_pai) {
            return $movimentacao->id_movimentacao_pai;
        }
        return null;
    }
    
    /**
     * lancamentos
     */
    public function getLancamentosCron($data) {
        
        $select = $this->select()
                ->from(array('mov' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('usu' => 'usuario'), 'mov.id_usuario = usu.id_usuario', array('*'))
                ->joinInner(array('cat' => 'categoria'), 'mov.id_categoria = cat.id_categoria', array('*'))
                ->joinLeft(array('cta' => 'conta'), 'mov.id_conta = cta.id_conta', array('*'))
                ->joinLeft(array('ctr' => 'cartao'), 'mov.id_cartao = ctr.id_cartao', array('*'))
                ->where("mov.data_movimentacao = ?", $data)
                ->where("mov.realizado = ?", 0)
                ->where("usu.ativo_usuario = ?", 1);
        
        //Zend_Debug::dump($select->__toString()); die();
        return $this->fetchAll($select);
        
    }
    
}

