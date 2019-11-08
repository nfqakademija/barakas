<?php

namespace App\Entity;

class EmailSend
{

    public static function signupOrganisationEmail($email, $password)
    {
        $emailText = '<p>Sveiki!
                      <br><br>
                      Džiaugiamės, jog nusprendėte pradėti naudotis mūsų Kaimyne padėk aplikacija.
                      <br>
                      Spauskite <a href="#">šią</a> nuorodą ir prisijungimui naudokite šiuos duomenis:
                      <br>
                      Prisijungimo vardas: <strong>'.$email.'</strong>
                      <br>
                      Laikinas slaptažodis: <strong>'.$password.'</strong>
                      <br>
                      Tikimės, kad bendrabučio gyventojams aplikacija bus naudinga ir pravers kiekvieną dieną.
                      <br><br>
                      Gero naudojimosi linki
                      <br>
                      Kaimyne padėk administracija
                      </p>';

        return $emailText;
    }

    public static function getSubject()
    {
        return 'Kaimyne padėk!';
    }
}
