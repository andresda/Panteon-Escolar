<?php

/*
*
* Panteon Escolar
*
* Yuri Wanderley (yuri.wanderley at gmail.com)
* Tarcisio Araujo (tatauphp at gmail.com)
* Marcelo Soares Souza (marcelo at juntadados.org)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* http://www.gnu.org/licenses/gpl-2.0.html
*
*/

class MinhasPropostasDeAcaoDBXML extends XmlnukeCollection implements IXmlnukeDocumentObject
{

  protected $_context;
  protected $_opcao;
  protected $_num_registros_padrao = 3;

  public function generateObject($current) {
    if($this->_context->ContextValue("Pesquisar") == true && $this->_context->ContextValue("id_usuario_filtro")!="")
      $id_usuario = $this->_context->ContextValue("id_usuario_filtro");
    else
      $id_usuario = $this->_context->authenticatedUserId();
    $id_tema_panteon = $this->_context->getCookie("id_tema_panteon_definido");
    $nivel_acesso = PanteonEscolarBaseModule::getNivelAcesso($this->_context, $id_usuario);

    $span1 = new XmlnukeSpanCollection();
    $this->addXmlnukeObject($span1);


    if($this->_opcao == "processPageField") {
      $nome_modulo = "minhaspropostasdeacao";
      if($this->_context->ContextValue("acao") == 'ppmsgs') $this->_context->redirectUrl($nome_modulo);

      // Mensagem de Avisos
      $span1->addXmlnukeObject(PanteonEscolarBaseModule::aviso($this->_context));

      $db = new UsuarioXTemaPanteonDB($this->_context);
      $dbDiagnosticoIndividual = new DiagnosticoIndividualDB($this->_context);
      $dbUsuarioXTemaPanteon = new UsuarioXTemaPanteonDB($this->_context);
      $id_usuarioxtemapanteon = $db->obterIDPorIDUsuarioXIDTemaPanteon($id_usuario, $id_tema_panteon);

      $dbxml = new PropostaDeAcaoDBXML($this->_context, "minhaspropostasdeacao", "Minhas Propostas de Ação");

      // permissao - $newRec, $view, $edit, $delete
      $permissao = array(true, false, true, false);
      $pagina = $dbxml->criarProcessPageFields($id_usuario, $id_tema_panteon, $permissao);

      $aviso = new XmlInputLabelObjects("<p></p>");
      $txt  = '<div id="caixaOpcao3">Chegou a hora de propor ações! O que podemos fazer para resolver o(s) problema(s)? Escreva sua proposta de ação.</div>';
      $txt .= '<div id="caixaOpcoes">';
      $txt .= '<div id="caixaOpcao1">Proposta de Ação do Grupo<br/><a href="/minhaspropostasdeacaogrupo">Clique aqui.</a></div>';
      $txt .= '<div id="caixaOpcao2">Minha Proposta de Ação Geral<br/><a href="/minhapropostadeacaogeral">Clique aqui.</a></div>';
      $txt .= '</div>';
      $aviso->addXmlnukeObject(new XmlNukeText($txt));
      $span1->addXmlnukeObject($aviso);

      if($pagina == false) {

        if(($this->_context->ContextValue("acao") == "ppnew") || ($this->_context->ContextValue("chamada") == 1)) {
          $span1->addXmlnukeObject($pagina);

        } else {
          $span1->addXmlnukeObject($this->filtro());
          if($dbDiagnosticoIndividual->obterTodosOsDiagnosticosIndividuaisPorIDIUsuarioXTemaPanteon($id_usuarioxtemapanteon)->Count() > 0) {
            $span1->addXmlnukeObject(new XmlNukeText('<div id="meusPontosDeVistas">Nenhuma Proposta de Ação encontrada, cadastre a primeira agora.<br/> <a href="'.PanteonEscolarBaseModule::curPageURL().'&acao=ppnew">Clicando Aqui</a></div>'));

          } else {
            $span1->addXmlnukeObject(new XmlNukeText('<div id="meusPontosDeVistas">Você precisa fazer pelo menos um Diagnóstico Específico<br/>para criar uma Proposta de Ação</div>'));

          }

        }
      } else {
        if($pagina->getAllRecords()->Count() > 0) {
          if($this->_context->ContextValue("acao") == "")
            $span1->addXmlnukeObject($this->filtro());
          $span1->addXmlnukeObject($pagina);
        } else {

          if(($this->_context->ContextValue("acao") == "ppnew") || ($this->_context->ContextValue("chamada") == 1)) {
            $span1->addXmlnukeObject($pagina);
          } else {

            if($dbDiagnosticoIndividual->obterTodosOsDiagnosticosIndividuaisPorIDIUsuarioXTemaPanteon($id_usuarioxtemapanteon)->Count() > 0)
              $span1->addXmlnukeObject(new XmlNukeText('<div id="meusPontosDeVistas">Nenhuma Proposta de Ação encontrada, cadastre a primeira agora.<br/> <a href="'.PanteonEscolarBaseModule::curPageURL().'&acao=ppnew">Clicando Aqui</a></div>'));
            else
              $span1->addXmlnukeObject(new XmlNukeText('<div id="meusPontosDeVistas">Você precisa fazer pelo menos um Diagnóstico Individual para criar uma Proposta de Ação</div>'));
          }

        }
      }

    }

    // Inicio - menu
    //
    if($this->_opcao == "menu") {
      $node = XmlUtil::CreateChild($current, "blockabausuario", "");
      $body = PanteonEscolarBaseModule::preencherMenu($node, PanteonEscolarBaseModule::preencherMenuUsuario(PanteonEscolarMenu::PlanoDeAcao));

    }
    //
    // Fim - menu

    if($this->_opcao == "listarDireita") {
      $db = new DiagnosticoIndividualDB($this->_context);
      $dbUsuarioXTemaPanteon = new UsuarioXTemaPanteonDB($this->_context);
      $itDB = $db->obterTodosOsDiagnosticosIndividuaisPorIDIUsuarioXTemaPanteon($dbUsuarioXTemaPanteon->obterIDPorIDUsuarioXIDTemaPanteon($id_usuario, $id_tema_panteon), 2);
      $node = XmlUtil::CreateChild($current, "blockmensagem", "");
      $body = PanteonEscolarBaseModule::criarTitulo($node, "Diagnósticos Realizados");

      if($itDB->Count() > 0)
        $body = PanteonEscolarBaseModule::preencherBarra($node, $itDB, "nome_situacao_problema", "texto_diagnostico_individual", "nome_item_analise");
      else
        $body = PanteonEscolarBaseModule::preencherBarraVazia($node);

      if(($nivel_acesso =="GESTOR") || ($nivel_acesso =="ADMINISTRADOR") || ($nivel_acesso =="EDITOR")) XmlUtil::AddAttribute($node, "criartemapanteon", "true");

    }

    // Inicio - menu head
    //
    if($this->_opcao == "menuHead") {
      $nodeHead = XmlUtil::CreateChild($current, "blockhead", "");
      XmlUtil::AddAttribute($nodeHead, "perfil", strtolower($nivel_acesso));

      $msg = "Bem-Vindo, ".ucfirst($this->_context->authenticatedUser())." (".$nivel_acesso.").";
      $node = XmlUtil::CreateChild($current, "blockbarramenu", "");
      $body = PanteonEscolarBaseModule::preencherMenuHead($node, PanteonEscolarBaseModule::preencherMenuHeadPadrao($nivel_acesso, 'meutemapanteon'));
      XmlUtil::AddAttribute($node, "nome_usuario", $msg);
      XmlUtil::AddAttribute($node, "logout", "true");

    }
    //
    // Fim - menu head


    $node = XmlUtil::CreateChild($current, "blockcenter", "");
    $body = XmlUtil::CreateChild($node, "body", "");

    parent::generatePage($body);

  }

  public function filtro() {
    $span = new XmlnukeSpanCollection();

    $formPost = "module:panteonescolar.minhaspropostasdeacao";
    $form = new XmlFormCollection($this->_context, $formPost, "Minhas Propostas de Ação");

    //$form->addXmlnukeObject($this->filtroDescricao());
    $form->addXmlnukeObject($this->filtroUsuario());
    $form->addXmlnukeObject(new XmlInputHidden("Pesquisar", true));
    //$form->addXmlnukeObject($this->filtroEstruturaSocial());

    $buttons = new XmlInputButtons();
    $buttons->addSubmit("Pesquisar");
    $form->addXmlnukeObject($buttons);

    $span->addXmlnukeObject($form);

    return $span;

  }

  public function filtroUsuario() {
    $db = new UsuarioXTemaPanteonDB($this->_context);
    $it = $db->obterTodosOsUsuariosColetaramTemaPanteonPorIDTemaPanteon($this->_context->getCookie("id_tema_panteon_definido"));
    $listaUsuario = PanteonEscolarBaseDBAccess::getArrayFromIterator($it, "id_usuario", "nome_completo_usuario");
    $listaUsuario[""] = "Minhas Propostas de Ação";

    $id_usuario_filtro_selecionado = $this->_context->ContextValue("id_usuario_filtro");
    $lista = new XmlEasyList(EasyListType::SELECTLIST, "id_usuario_filtro", "Usuário", $listaUsuario, $id_usuario_filtro_selecionado);


    return $lista;

  }

  public function MinhasPropostasDeAcaoDBXML($context, $opcao) {
    if(!($context instanceof Context)) throw new Exception("Falta de Context");

    $this->_context = $context;
    $this->_opcao = $opcao;

  }

}

?>