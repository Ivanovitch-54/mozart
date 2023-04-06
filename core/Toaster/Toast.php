<?php

namespace Core\Toaster;

class Toast

{
    public function success(string $message): string
    {
        return "<div class='alert bg-success text-center text-white'>
                $message
                </div>";
    }

    public function error(string $message): string
    {
        return "<div class='alert alert-danger text-center'>
                $message
                </div>";
    }

    public function warning(string $message): string
    {
        return "<div class='alert alert-warning text-center'>
                $message
                </div>";
    }
}
