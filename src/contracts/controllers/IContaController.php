<?php

namespace app\contracts\controllers;

interface IContaController {
    

    // --- ACTIONS --- //
    public function saqueContaAction();
    public function depositoContaAction();
    public function valorTotalContaAction();
    public function logoutContaAction();
    public function loginContaAction();
    public function alternarContaAction();
    public function criarContaAction();

    // ---- VIEWS ---- //
    public function menuCaixaView();
    public function loginContaView();
    public function criarContaView();
}
