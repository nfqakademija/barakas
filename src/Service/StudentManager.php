<?php


namespace App\Service;


class StudentManager
{
    public function removeStudentFromStudentsArray($students, $user)
    {
        $studentToRemove = null;

        foreach ($students as $struct) {
            if ($user->getOwner() == $struct->getOwner()) {
                $studentToRemove = $struct;
                break;
            }
        }

        $key = array_search($studentToRemove, $students);
        unset($students[$key]);

        return $students;
    }
}