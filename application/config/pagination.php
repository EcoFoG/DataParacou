<?php

$config['pagination'] = array (
    "full_tag_open" => '<ul class="pagination justify-content-center">' ,
    "full_tag_close" => '</ul>' ,
    "first_link" => true,
    "last_link" => true,
    "attributes" => array('class' => 'page-link'),
    "first_tag_open" => '<li class="page-item">' ,
    "first_tag_close" => '</li>' ,
    "first_link" => 'First',
    "prev_link" => '&laquo' ,
    "prev_tag_open" => '<li class="page-item prev">' ,
    "prev_tag_close" => '</li>' ,
    "next_link" => '&raquo' ,
    "next_tag_open" => '<li class="page-item">' ,
    "next_tag_close" => '</li>' ,
    "last_tag_open" => '<li class="page-item">' ,
    "last_tag_close" => '</li>' ,
    "last_link" => 'Last',
    "cur_tag_open" => '<li class="page-item active"><a href="#" class="page-link">' ,
    "cur_tag_close" => '</a></li>' ,
    "num_tag_open" => '<li class="page-item">' ,
    "num_tag_close" => '</li>' ,
    "page_query_string" =>  TRUE,
    "use_page_numbers" =>  TRUE,
    "reuse_query_string" =>  TRUE,
    "query_string_segment" =>  'page'
);
