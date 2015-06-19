/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * funcoes padrao para todo a aplicacao
 */
window.onload = function() {
    
    // campo valor movimentacao
    $("#valor_movimentacao").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: true});    
    
    // campo valor meta
    $("#valor_meta").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: true});   
    
    // saldo inicial conta
    $("#saldo_inicial").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: true});   
    
    // limite cartao
    $("#limite_cartao").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: true});   
    
    // valor plano
    $("#valor_plano").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: true});   
    
    // campo data movimentacao
    $("#data_movimentacao").mask("99/99/9999");    
    
    // campo data_nascimento
    $("#data_nascimento").mask("99/99/9999");    
    
    // campo cpf_usuario
    $("#cpf_usuario").mask("999.999.999-99");
    
    $('#data_movimentacao').datepicker({
        language: "pt", 
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });
    
}
    
