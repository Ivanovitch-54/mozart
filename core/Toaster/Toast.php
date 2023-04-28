<?php

namespace Core\Toaster;

class Toast

{
    public function success(string $message): string
    {
        return "<div class='alert alert-success bg-success text-center text-white w-50 mx-auto'>
                $message
                </div>";
    }

    public function error(string $message): string
    {
        return "<div class='alert alert-danger text-center w-50 mx-auto'>
                $message
                </div>";
    }

    public function warning(string $message): string
    {
        return "<div class='alert alert-warning text-center w-50 mx-auto'>
                $message
                </div>";
    }
}
