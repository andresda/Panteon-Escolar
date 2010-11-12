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

class LoginDBXML extends XmlnukeCollection implements IXmlnukeDocumentObject
{

  protected $_context;
  protected $_opcao;
  protected $_num_registros_padrao = 3;

  public function generateObject($current) {

    $span1 = new XmlnukeSpanCollection();
    $this->addXmlnukeObject($span1);

    $titulo = "Login(s)";
    $nome_modulo = "login";

    if($this->_opcao == "listarDireita") {
      $node = XmlUtil::CreateChild($current, "blockmensagem", "");
      $body = PanteonEscolarBaseModule::criarTitulo($node);
      $body = PanteonEscolarBaseModule::preencherBarraVazia($node);

    }

    // Inicio - menu
    //
    if($this->_opcao == "menu") {
      $node = XmlUtil::CreateChild($current, "blockabausuario", "");
      $body = PanteonEscolarBaseModule::preencherMenuPadrao($node);

    }
    //
    // Fim - menu

    // Inicio - menu head
    //
    if($this->_opcao == "menuHead") {
      $node = XmlUtil::CreateChild($current, "blockbarramenu", "");
      $body = PanteonEscolarBaseModule::preencherMenuHead($node, PanteonEscolarBaseModule::preencherMenuHeadInicial());

      if($this->_context->ContextValue("action") == "action.NEWUSER")
        $body = PanteonEscolarBaseModule::preencherMenuHeadAuxiliar($node, PanteonEscolarBaseModule::preencherMenuHeadInicialAcesso('/xmlnuke.php?module=login&amp;action=action.NEWUSER&amp;ReturnUrl=%2fmeuperfil&amp;site=PanteonEscolar&amp;xsl=page&amp;xml=home&amp;lang=pt-br'));
      else
        $body = PanteonEscolarBaseModule::preencherMenuHeadAuxiliar($node, PanteonEscolarBaseModule::preencherMenuHeadInicialAcesso('xmlnuke.php?module=login&amp;site=PanteonEscolar&amp;lang=pt-br&amp;ReturnUrl=%2fmeuperfil'));

    }
    //
    // Fim - menu head

    $node = XmlUtil::CreateChild($current, "blockcenter", "");
    $body = XmlUtil::CreateChild($node, "body", "");

    parent::generatePage($body);

  }

  public function LoginDBXML($context, $opcao) {
    if(!($context instanceof Context)) throw new Exception("Falta de Context");

    $this->_context = $context;
    $this->_opcao = $opcao;

  }

}

?>