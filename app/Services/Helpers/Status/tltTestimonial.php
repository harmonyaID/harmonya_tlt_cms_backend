<?php

if (!function_exists("errTltTestimonialGet")) {
    function errTltTestimonialGet($internalMsg = "")
    {
        error(404, "TLT testimonial not found", $internalMsg);
    }
}

if (!function_exists("errTltTestimonialSave")) {
    function errTltTestimonialSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save TLT testimonial", $internalMsg);
    }
}

if (!function_exists("errTltTestimonialUpdate")) {
    function errTltTestimonialUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update TLT testimonial", $internalMsg);
    }
}

if (!function_exists("errTltTestimonialDelete")) {
    function errTltTestimonialDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete TLT testimonial", $internalMsg);
    }
}
