<?php


namespace App\Service;

use Symfony\Component\Security\Core\Security;

class StudentManager
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser()
    {
        return $this->security->getUser();
    }

    public function removeStudentFromStudentsArray($students)
    {
        $studentToRemove = null;

        foreach ($students as $struct) {
            if ($this->getUser()->getOwner() == $struct->getOwner()) {
                $studentToRemove = $struct;
                break;
            }
        }

        $key = array_search($studentToRemove, $students);
        unset($students[$key]);

        return $students;
    }
}
