/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){
    
    // escondendo o campo banco
    $("#id_banco").hide();
    $("#id_banco-label").hide();
    
    // verificando o tipo de conta selecionada
    $("#id_tipo_conta").change(function(){
        var id_tipo_conta = $(this).val();        
        if (id_tipo_conta != 4 && id_tipo_conta != 5) {
            $("#id_banco").show();
            $("#id_banco-label").show();
        } else {
            $("#id_banco").hide();
            $("#id_banco-label").hide();
        }
    });
    
});

