<?php

if (!function_exists("errBlogCategoryGet")) {
    function errBlogCategoryGet($internalMsg = "")
    {
        error(404, "Blog category not found", $internalMsg);
    }
}

if (!function_exists("errBlogCategorySave")) {
    function errBlogCategorySave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save blog category", $internalMsg);
    }
}

if (!function_exists("errBlogCategoryUpdate")) {
    function errBlogCategoryUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update blog category", $internalMsg);
    }
}

if (!function_exists("errBlogCategoryDelete")) {
    function errBlogCategoryDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete blog category", $internalMsg);
    }
}

if (!function_exists("errBlogTagGet")) {
    function errBlogTagGet($internalMsg = "")
    {
        error(404, "Blog tag not found", $internalMsg);
    }
}

if (!function_exists("errBlogTagSave")) {
    function errBlogTagSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save blog tag", $internalMsg);
    }
}

if (!function_exists("errBlogTagUpdate")) {
    function errBlogTagUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update blog tag", $internalMsg);
    }
}

if (!function_exists("errBlogTagDelete")) {
    function errBlogTagDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete blog tag", $internalMsg);
    }
}


if (!function_exists("errBlogGet")) {
    function errBlogGet($internalMsg = "")
    {
        error(404, "Blog not found", $internalMsg);
    }
}

if (!function_exists("errBlogSave")) {
    function errBlogSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save blog", $internalMsg);
    }
}

if (!function_exists("errBlogUpdate")) {
    function errBlogUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update blog", $internalMsg);
    }
}

if (!function_exists("errBlogDelete")) {
    function errBlogDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete blog", $internalMsg);
    }
}