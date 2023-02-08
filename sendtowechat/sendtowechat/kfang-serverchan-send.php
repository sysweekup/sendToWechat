<?php
/*
Plugin Name: 发送评论提醒到微信
Description: 通过Server酱，发送评论提醒到微信
Version: 1.0
Author: 小菠萝
Author URI: http://sosloli.com/
*/

function kfang_serverchan_send($comment_id) {
    $comment = get_comment($comment_id); 
    $text ='博客的文章《'.get_the_title($comment->comment_post_ID).'》有新评论';
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $url=$comment->comment_author_url;
    if ($url != ''){
        $desp = '昵称:'.($comment->comment_author).' 
        '.'邮箱:'.($comment->comment_author_email).' 
        '.'内容:'.($comment->comment_content).'
        '.'原文地址:'.htmlspecialchars(get_permalink( $id )).'';
    }else{
        $desp = '昵称:'.($comment->comment_author).' 
        '.'邮箱:'.($comment->comment_author_email).' 
        '.'内容:'.($comment->comment_content).'
        '.'原文地址:'.htmlspecialchars(get_permalink( $id )).''; 
    }
    $key = get_option('serverchan_key');   //send·ID
    $postdata = http_build_query( array( 'text' => $text, 'desp' => $desp ));
    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata));
    $context  = stream_context_create($opts);
    return $result = file_get_contents('https://sctapi.ftqq.com/'.$key.'.send', false, $context);
} 
add_action('comment_post', 'kfang_serverchan_send', 19, 2);

// 添加设置页面

function kfang_serverchan_settings_page() {
    add_options_page( '评论微信推送', '评论微信推送', 'manage_options', 'serverchan-send', 'kfang_serverchan_settings_page_callback' );
}
add_action( 'admin_menu', 'kfang_serverchan_settings_page' );
function kfang_serverchan_settings() {
    register_setting( 'serverchan_settings', 'serverchan_key' );
}
add_action( 'admin_init', 'kfang_serverchan_settings' );
function kfang_serverchan_settings_page_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '没有权限' );
    }
    $serverchan_key = get_option( 'serverchan_key' );
echo '<div class="wrap">';
echo '<h1>服务链接通知设置</h1>';
echo '<p>请登录 <a href="https://sct.ftqq.com/login" target="_blank">server酱</a> 获取KEY</p>';
echo '<form method="post" action="options.php">';
settings_fields( 'serverchan_settings' );
do_settings_sections( 'serverchan_settings' );
echo '<table class="form-table">';
echo '<tr valign="top">';
echo '<th scope="row">KEY 值</th>';
echo '<td><input type="text" name="serverchan_key" value="' . esc_attr( $serverchan_key ) . '"/></td>';
echo '</tr>';
echo '</table>';
submit_button();
echo '</form>';
echo '</div>';
}


