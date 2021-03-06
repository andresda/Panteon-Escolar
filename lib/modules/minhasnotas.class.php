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

ModuleFactory::IncludePhp("panteonescolar", "classes/base/PanteonEscolarBaseModule.class.php");

class MinhasNotas extends PanteonEscolarBaseModule
{

  public function CreatePage()
  {
    $myWords = $this->WordCollection();

    $this->defaultXmlnukeDocument = PanteonEscolarBaseModule::definirTituloSimples("Minhas Anotações");

    $blockHead   = new XmlBlockCollection(NULL, BlockPosition::Right);
    $blockCenter = new XmlBlockCollection(NULL, BlockPosition::Center);
    $blockInfo   = new XmlBlockCollection(NULL, BlockPosition::Left);
    $blockMenu   = new XmlBlockCollection(NULL, BlockPosition::Menu);

    $this->defaultXmlnukeDocument->addXmlnukeObject($blockHead);
    $blockHead->addXmlnukeObject(new MinhasNotasDBXML($this->_context, "menuHead"));

    $this->defaultXmlnukeDocument->addXmlnukeObject($blockInfo);
    $blockInfo->addXmlnukeObject(new MinhasNotasDBXML($this->_context, "listarDireita"));

    $this->defaultXmlnukeDocument->addXmlnukeObject($blockMenu);
    $blockMenu->addXmlnukeObject(new MinhasNotasDBXML($this->_context, "menu"));

    $this->defaultXmlnukeDocument->addXmlnukeObject($blockCenter);
    $blockCenter->addXmlnukeObject(new MinhasNotasDBXML($this->_context, "processPageField"));

    $dbNivelAcesso = new UsuarioXNivelAcessoDB($this->_context);
    $nivel_acesso = $dbNivelAcesso->obterNivelAcessoPorIDUsuario($this->_context->authenticatedUserId());
    $this->_context->addCookie("papel_usuario", $nivel_acesso);

    return $this->defaultXmlnukeDocument;

  }

  public function __constructor() {}
  public function __destruct() { }
  public function useCache()
  {
    return false;
  }

  public function requiresAuthentication()
  {
    return true;
  }

  public function getAccessLevel()
  {
    return AccessLevel::OnlyRole;
  }

  public function getRole()
  {
    return array("ANALISTA", "EDITOR", "GESTOR", "ADMINISTRADOR");
  }

}

?>
