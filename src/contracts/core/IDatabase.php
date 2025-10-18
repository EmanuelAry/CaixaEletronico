<?php

namespace app\contracts\core;

interface IDatabase {
    public function getConnection();
    //EMANUEL VERIFICAR
    // public function connect();
    // public function query($sql);
    // public function close();
}
