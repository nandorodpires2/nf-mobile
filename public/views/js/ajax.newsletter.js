/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function (){
   
    $("#btn-newsletter").click(function (){
       
        var base_url = baseUrl();
        
        var nome = $("#nome-news").val();
        var email = $("#email-news").val();
        
        if (nome == '') {
            alert("Preencha o campo nome");
            return false;
        }
        
        if (email == '') {
            alert("Preencha o campo e-mail");
            return false;
        }

        $.ajax({
            url: base_url + 'ajax/cadastra-newsletter',
            type: "post",
            data: {
                nome: nome,
                email: email
            },
            dataType: "html",
            beforeSend: function() {            
                $("#form-newsletter").hide();
                $("#newsletter-loader").show();
                $("#newsletter-result").html("Por favor agurade...");                
                $("#newsletter-result").show();
            },
            success: function(dados) { 
                $("#newsletter-loader").hide();
                $("#newsletter-result").html(dados);
                $("#newsletter-result").show();
            },
            error: function(error) {
                alert('Houve um erro');
            }
        });
        
    });
    
});

