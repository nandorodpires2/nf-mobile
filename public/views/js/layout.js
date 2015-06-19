/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function baseUrl() {
    var host = window.location.hostname;  
    var base_url = "";    
    
    // verifica se e base de teste
    if (host == "localhost" || host == "127.0.0.1") {
        base_url = "http://" + host + "/nf-mobile/public/";        
    } else if (host == "newfinances2.newfinances.com.br") {
        base_url = "http://" + host + "/public/";        
    } else {
        base_url = "http://" + host + "/";        
    }
    
    return base_url;
}
   


