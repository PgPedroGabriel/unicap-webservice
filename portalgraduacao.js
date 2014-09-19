function hidestatus() {
	window.status=' ';
	return true;
}

function apresentarLogin() {
    document.formPortal.target = "";   
    document.formPortal.rotina.value = 0;
	document.formPortal.submit();  
}

function login() {
	 if (matriculaValida(document.formPortal.Matricula.value,document.formPortal.Digito.value) && 
		 senhaValida(document.formPortal.Senha.value)) {
		 document.formPortal.target = "";   
	     document.formPortal.rotina.value = 1;
		 document.formPortal.submit();
   }   
}

function verPaginaInicial() {
	 document.formPortal.target = "";   
     document.formPortal.rotina.value = 101;
	 document.formPortal.submit(); 
}

function encerrarPortal() {
	 document.formPortal.target = "";   
     document.formPortal.rotina.value = 0;
	 document.formPortal.submit();  
}

function chamarAchadosPerdidos() {
  alert('entrou aqui');
	   window.open("", "branco", "width=930,height=700,scrollbars=Yes, resizable=YES"); 
	   document.formPortal.target = "branco";
	   document.formPortal.rotina.value = 1000;
	   document.formPortal.submit();
}

function processarAchadosPerdidos() {
	   document.formPortal.target = ""; 
	   document.formPortal.rotina.value = 1;
	   document.formPortal.tipochamada.value = 1;
	   document.formPortal.submit();
}

function chamarAvaliacaoInstitucional() {
	   window.open("", "branco", "width=930,height=700,scrollbars=Yes, resizable=YES"); 
	   document.formPortal.target = "branco";
	   document.formPortal.rotina.value = 2000;
	   document.formPortal.submit();
}

function processarAvaliacaoInstitucional() {
	document.formAvalInst.target = ""; 
	document.formAvalInst.rotina.value = 1;
	document.formAvalInst.Senha.value  = "portal"; 
	document.formAvalInst.submit();
} 

function chamarAvaliacaoMatricula() {
	   window.open("", "branco", "width=930,height=700,scrollbars=Yes, resizable=YES"); 
	   document.formPortal.target = "branco";
	   document.formPortal.rotina.value = 3000;
	   document.formPortal.submit();
}

function processarAvaliacaoMatricula() {
	document.formAvalMatr.target = ""; 
	document.formAvalMatr.rotina.value = 1;
	document.formAvalMatr.Senha.value  = "portal"; 
	document.formAvalMatr.submit();
} 

function chamarConvenio() {
	   window.open("", "branco", "width=930,height=635,scrollbars=Yes, resizable=YES"); 
	   document.formPortal.target = "branco";
	   document.formPortal.rotina.value = 3000;
	   document.formPortal.submit();
}



function chamarRotina(rotina) {
	 document.formPortal.target = "";   
     document.formPortal.rotina.value = rotina;
	 document.formPortal.submit(); 
}
function prepararImpressao(rotina) {
	 window.open("", "branco", "width=700,height=635,scrollbars=Yes, resizable=YES"); 
	 document.formPortal.target = "branco";
	 document.formPortal.rotina.value = rotina;
     document.formPortal.submit();
}

function alterarEndereco() {

	document.forms[0].wLogradouro.value = retiraAcentos(document.forms[0].wLogradouro.value);
	document.forms[0].wBairro.value     = retiraAcentos(document.forms[0].wBairro.value);
	document.forms[0].wCidade.value     = retiraAcentos(document.forms[0].wCidade.value);

    if (verificaCampoInvalido(document.forms[0].wLogradouro,'X', 1,'Logradouro Inválido.')) return
    if (verificaCampoInvalido(document.forms[0].wBairro,'T', 1,'Bairro Inválido.')) return
    if (verificaCampoInvalido(document.forms[0].wCidade,'C', 1,'Cidade Inválida.')) return
    if (opcaoInvalida(document.forms[0].wUF,'Unidade Federativa Inválida!')) return
    if (verificaCampoInvalido(document.forms[0].wCEP,'N', 8,'CEP Inválido.')) return
    if ((document.forms[0].Email.value != "") && !emailValido(document.forms[0].Email.value)) documment.stop;

    document.formPortal.target = "";   
    document.formPortal.rotina.value = 21;
	document.formPortal.submit();  
}

function alterarTelefones() {
	
    if (document.forms[0].wNumeroCel.value != 0) {
        if (verificaCampoInvalido(document.forms[0].wDDDCel,'N', 2,'DDD do Celular Inválido.')) return		   
        if (verificaCampoInvalido(document.forms[0].wNumeroCel,'N', 7,'Número do Celular Inválido.')) return
    }
	
    if (document.forms[0].wNumeroRes.value != 0) {
        if (verificaCampoInvalido(document.forms[0].wDDDRes,'N', 2,'DDD do Fone Residencial Inválido.')) return		   
        if (verificaCampoInvalido(document.forms[0].wNumeroRes,'N', 7,'Número do Fone Residencial Inválido.')) return
		if (document.forms[0].wRamalRes.value != "")		   
	        if (verificaCampoInvalido(document.forms[0].wRamalRes,'N', 0,'Ramal do Fone Residencial Inválido.')) return	
    }
	if (document.forms[0].wNumeroCom.value != 0) {
        if (verificaCampoInvalido(document.forms[0].wDDDCom,'N', 2,'DDD do Fone Comercial Inválido.')) return		   
        if (verificaCampoInvalido(document.forms[0].wNumeroCom,'N', 7,'Número do Fone Comercial Inválido.')) return
        if (document.forms[0].wRamalCom.value != "")
	    	if (verificaCampoInvalido(document.forms[0].wRamalCom,'N', 0,'Ramal do Fone Comercial Inválido.')) return		   		   
    }
	
    resp = false;
    if (document.forms[0].wContatoCel.checked) 
        resp = true;
    if (document.forms[0].wContatoRes.checked)
        resp = true;
    if (document.forms[0].wContatoCom.checked)
        resp = true;
    if (!resp) {
	    if (!(document.forms[0].wNumeroCel.value == 0) &&
		     (document.forms[0].wNumeroRes.value == 0) &&
	  	     (document.forms[0].wNumeroCom.value == 0)) {
		    alert('Contato Principal não selecionado !');
            document.forms[0].wContatoCel.focus();
            return;
		}
    }
    if (document.forms[0].wNumeroCel.value == 0) {	
        if (document.forms[0].wContatoCel.checked) {
		    alert('Contato Principal Inválido !');
            document.forms[0].wContatoCel.focus();
            return;
		}
    }
    if (document.forms[0].wNumeroRes.value == 0) {	
        if (document.forms[0].wContatoRes.checked) {
		    alert('Contato Principal Inválido !');
            document.forms[0].wContatoRes.focus();
            return;
		}
    }
    if (document.forms[0].wNumeroCom.value == 0) {	
        if (document.forms[0].wContatoCom.checked) {
		    alert('Contato Principal Inválido !');
            document.forms[0].wContatoCom.focus();
            return;
		}
    }
	if (document.forms[0].wContatoCel.checked) 
        document.forms[0].wContato.value = document.forms[0].wContatoCel.value;
    if (document.forms[0].wContatoRes.checked)
        document.forms[0].wContato.value = document.forms[0].wContatoRes.value;
    if (document.forms[0].wContatoCom.checked)
        document.forms[0].wContato.value = document.forms[0].wContatoCom.value;
		
    document.formPortal.target = "";   
    document.formPortal.rotina.value = 25;
	document.formPortal.submit();  
}

function enviarCordaPele() {
    resp = false;
    for (var i=0; i<=document.formPortal.CordaPele.length-1; i++) {
         if (document.formPortal.CordaPele[i].checked) 
             resp = true;
    }
    if (!resp) {
        alert('Cor da Pele não Selecionada !');
        return;
    }
    document.formPortal.target = "";
    document.formPortal.rotina.value = 27;
    document.formPortal.submit();
}

function enviarProposta(rotina) {
   document.formPortal.action = "PortalGraduacao";
   document.formPortal.rotina.value = rotina;
   document.formPortal.target = "";
   document.formPortal.submit();
}

function matriculaValida(matricula,digito) {
         resp = true;
         if (valcampoerro(matricula,9)) {
            resp = false;
            alert("Número de Matrícula Inválido !");
         }
         else {
           if (valcampoerro(digito,1)) {
               resp = false;
               alert("Dígito Verificador Inválido !");
           }
         }
         return resp;
}


function senhaValida(senha) {
     resp = true;
     if (valcampoerro(senha,6)){
         resp = false;
         alert("Senha Inválida !");
     }
     return resp;
}

function valcampoerro(cd,max) {
         numero = "0123456789";
         erro = false;
         if (cd.length != max) {
            erro = true;
         }
         for (var i=0;i < cd.length;i++) {
              if (numero.indexOf(cd.charAt(i)) < 0) {
                 erro = true;
                 break;
              }
         }
         return erro;
}

function emailValido(email){
      resp = false;
      if (email == "") {
          alert("Digite o Email");
      }
      else {
           if  (email.length < 3) {
                alert("Email inválido !");
           }
           else {
                erro  = false;
                achou = 0;
                for ( var i=0;i < email.length; i++) {
                    if (email.charAt(i) == " ") {
                        erro = true;
                        break;
                    }
                    if (email.charAt(i) == "'") {
                        erro = true;
                        break;
                    }
                    if (email.charAt(i) == ";") {
                        erro = true;
                        break;
                    }
                    if (email.charAt(i) == "@") {
                        if (achou == 0) {
                            achou = i;
                        }
                        else {
                            erro = true;
                            break;
                        }
                    }
                }
                if (erro || achou == (email.length - 1) || achou == 1 || achou == 0) {
                    alert("Email Inválido !");
                }
                else {
                    resp = true;
                }
           }
      }
      return resp;
}

function dddValido(ddd) {
         numero = "0123456789";
         dddOK = true;
         if (ddd.length < 2) {
            dddOK = false;
         }
         for (var i=0;i < ddd.length;i++) {
              if (numero.indexOf(ddd.charAt(i)) < 0) {
                 dddOK = false;
                 break;
              }
         }
         return dddOK;
}

function validaValor(valor, mensagem){
    valor_aux = "";
    if (valor != ""){
	     if ( (valor.length < 4) || ((valor.charAt(valor.length - 3) != ".") & (valor.charAt(valor.length - 3) != ","))){
			 alert ('O valor do campo ' + mensagem + ' foi informado de maneira incorreta. Favor utilizar sempre o formato "999999.99"');
			 document.stop();
         }else {
           for (var j=0;j < valor.length; j++) {
              if ((valor.charAt(j) != ".") & (valor.charAt(j) != ",")) {valor_aux = valor_aux + valor.charAt(j);}
           }
           if (!numeroValido(valor_aux)){
  			  alert ('O valor do campo ' + mensagem + ' foi informado de maneira incorreta. Verifique os dados e tente novamente.');
			  document.stop();
	  	   }else {
		      valor_aux = valor_aux.substring(0,valor_aux.length - 2) + "." + valor_aux.substring(valor_aux.length - 2, valor_aux.length);
		   }
    	 }
    }
    return valor_aux;
}

function numeroValido(num) {
         numero = "0123456789";
         numOK = true;
         for (var i=0;i < num.length;i++) {
              if (numero.indexOf(num.charAt(i)) < 0) {
                 numOK = false;
                 break;
              }
         }
         return numOK;
}

function retiraAcentos(campo) {
         campo = campo.toUpperCase();
         for (var i=0;i < campo.length;i++) {
            if (campo.charAt(i) == 'Á' || campo.charAt(i) == 'À' ||
			    campo.charAt(i) == 'Ã' || campo.charAt(i) == 'Â') {
                campo = campo.substring(0,i) + "A" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == 'É' || campo.charAt(i) == 'Ê') {
                campo = campo.substring(0,i) + "E" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == 'Í') {
                campo = campo.substring(0,i) + "I" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == 'Ó' || campo.charAt(i) == 'Ô' ||
			    campo.charAt(i) == 'Õ') {
                campo = campo.substring(0,i) + "O" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == 'Ú' || campo.charAt(i) == 'Ü') {
                campo = campo.substring(0,i) + "U" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == 'Ç') {
                campo = campo.substring(0,i) + "C" +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == "'") {
                campo = campo.substring(0,i) + " " +campo.substring(i+1,campo.length);
            }
            if (campo.charAt(i) == '"') {
                campo = campo.substring(0,i) + " " +campo.substring(i+1,campo.length);
            }

         }

		 return campo;
}

function emitir2via(parcela) {
//	window.open("", "branco", "width=710,height=600,scrollbars=Yes, resizable=YES,left=100,top=50"); 
//	document.formPortal.target = "branco"; 
    document.formPortal.rotina.value = 12;
    document.formPortal.Parcela.value = parcela;
	document.formPortal.submit();  
}

function avisarsem2via(parcela) {
	   alert("Este Carnet não está disponível.");
}

function verificaCampoInvalido(campo, tipodocampo, tamanhominimo, msg) {
         campo.value = campo.value.toUpperCase();
         camposvalidos = '';
         msgcompl = '';
         if (tipodocampo== 'N') {
             camposvalidos = '0123456789';
             msgcompl = 'Só é permitido o uso de Números.';
         }
         if (tipodocampo== 'C') {
             camposvalidos = ' ABCDEFGHIJKLMNOPQRSTUVWXYZ';
             msgcompl = 'Só é permitido o uso de Letras sem acentuação.';
         }
         if (tipodocampo== 'A') {
            camposvalidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            msgcompl = 'Só é permitido o uso de Números e Letras sem acentuação.';
         }
         if (tipodocampo== 'T') {
            camposvalidos = ' 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            msgcompl = 'Só é permitido o uso de Números e Letras sem acentuação.';
         }
         if (tipodocampo== 'E') {
            camposvalidos = ' ,/0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            msgcompl = 'Só é permitido o uso de Números e Letras sem acentuação.';
         }
         if (tipodocampo== 'X') {
             camposvalidos = ' ,.-:/0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
             msgcompl = 'Só é permitido o uso de Números e Letras sem acentuação.';
          }
         if (tipodocampo== 'I') {
            camposvalidos = '- 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            msgcompl = 'Só é permitido o uso de Números,ponto(.), hífen(-) e Letras sem acentuação.';
         }
         erro = false;
         if (campo.value.length < tamanhominimo )
            erro = true;
         else {
              if (tipodocampo == 'C' && campo.value.charAt(0)== ' ')
                  erro= true;
              else {
                 for (var i=0;i < campo.value.length;i++)
                   if (camposvalidos.indexOf(campo.value.charAt(i)) < 0) {
                       msg = msg + ' ( ' + campo.value.charAt(i) + ' ) caracter inválido.';
                       erro = true;
                   }
                   if (erro)  msg = msg + msgcompl;
              }
         }
         if (erro) {
            campo.focus();
            alert(msg);
         }
         return erro;
}

function verificaCampoBranco(campo){
	branco = true;
    for (var i=0;i < campo.length;i++) {
      if (campo.charAt(i) != " ") branco = false;
	}
	return branco;
}

function opcaoInvalida(campo,msg) {
         if (campo.selectedIndex == 0) {
            campo.focus();
            alert(msg);
            return true;
         }
         return false;
}

function carregaAceitaEmail() {
	    if (document.formAcesso.aceitaEmail.value == 'C'){
			document.formAcesso.email[0].checked = true;
 		}else if (document.formAcesso.aceitaEmail.value == 'P'){
			document.formAcesso.email[1].checked = true;
		}else{
			document.formAcesso.email[2].checked = true;
		}
 }

function hidestatus(){
	window.status=' ';
	return true;
}
