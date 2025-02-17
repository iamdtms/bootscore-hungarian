<?php

/**
 * Comments
 *
 * @package Bootscore 
 * @version 6.0.4
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Comment reply
 */
function bootscore_reply() {

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}

add_action('wp_enqueue_scripts', 'bootscore_reply');


/**
 * Comments
 */
if (!function_exists('bootscore_comment')) :
  /**
   * Template for comments and pingbacks.
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   */
  function bootscore_comment($comment, $args, $depth) {
    // $GLOBALS['comment'] = $comment;

    if ('pingback' == $comment->comment_type || 'trackback' == $comment->comment_type) : ?>

      <li id="comment-<?php comment_ID(); ?>" <?php comment_class('media alert alert-info'); ?>>
      <div class="comment-body">
        <?php _e('Pingback:', 'bootscore'); ?><?php comment_author_link(); ?><?php edit_comment_link(__('Edit', 'bootscore'), '<span class="edit-link">', '</span>'); ?>
      </div>

    <?php else : ?>

      <li id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>

        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body mb-4 d-flex">

          <div class="flex-shrink-0 me-3">
            <?= get_avatar($comment, 80, '', '', array('class' => apply_filters('bootscore/class/comment/avatar', 'img-thumbnail rounded-circle'))); ?> 
          </div>

          <div class="comment-content">
            <div class="card">
              <div class="card-body">

                <?php printf('<h3 class="h5">%s</h3>', get_comment_author_link()); ?>

                <p class="small comment-meta text-body-secondary">
                  <time datetime="<?php comment_time('c'); ?>">
                    <?php printf(_x('%1$s at %2$s', '1: date, 2: time', 'bootscore'), get_comment_date(), get_comment_time()); ?>
                  </time>
                  <?php edit_comment_link(__('Edit', 'bootscore'), '<span class="edit-link">', '</span>'); ?>
                </p>


                <?php if ('0' == $comment->comment_approved) : ?>
                  <p class="comment-awaiting-moderation alert alert-info"><?php _e('Your comment is awaiting moderation.', 'bootscore'); ?></p>
                <?php endif; ?>

                <?php comment_text(); ?>

                <?php comment_reply_link(
                  array_merge(
                    $args,
                    array(
                      'add_below' => 'div-comment',
                      'depth'     => $depth,
                      'max_depth' => $args['max_depth'],
                      'before'    => '<p class="reply comment-reply">',
                      'after'     => '</p>'
                    )
                  )
                ); ?>
              </div> <!-- card-body -->
            </div><!-- card -->
          </div><!-- .comment-content -->

        </article><!-- .comment-body -->
      </li><!-- #comment -->

    <?php
    endif;
  }
endif;


/**
 * h2 Reply Title
 */
add_filter('comment_form_defaults', 'custom_reply_title');
function custom_reply_title($defaults) {
  $defaults['title_reply_before'] = '<h2 id="reply-title" class="h4">';
  $defaults['title_reply_after']  = '</h2>';

  return $defaults;
}


/**
 * Comment Cookie Checkbox
 * See https://github.com/bootscore/bootscore/issues/921
 */
function bootscore_change_comment_form_cookies_consent($fields) {
  // Check if the "Show comments cookies opt-in checkbox" setting is enabled
  if (get_option('show_comments_cookies_opt_in')) {
    $consent           = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';
    $fields['cookies'] = '<p class="comment-form-cookies-consent form-check mb-3">' .
                         '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" class="form-check-input"' . $consent . ' />' .
                         '<label for="wp-comment-cookies-consent" class="form-check-label">' . __('Save my name, email, and website in this browser for the next time I comment.', 'bootscore') . '</label>' .
                         '</p>';
  } else {
    // Remove the 'cookies' field if the setting is disabled
    unset($fields['cookies']);
  }

  return $fields;
}
add_filter('comment_form_default_fields', 'bootscore_change_comment_form_cookies_consent');


/**
 * Open comment author link in new tab
 */
add_filter('get_comment_author_link', 'open_comment_author_link_in_new_window');
function open_comment_author_link_in_new_window($author_link) {
  return str_replace("<a", "<a target='_blank'", $author_link);
}


/**
 * Open links in comments in new tab
 */
if (!function_exists('bs_comment_links_in_new_tab')) :
  function bs_comment_links_in_new_tab($text) {
    return str_replace('<a', '<a target="_blank" rel=”nofollow”', $text);
  }

  add_filter('comment_text', 'bs_comment_links_in_new_tab');
endif;


/**
 * Comment Button
 */
if (!function_exists('bootscore_comment_button')) :
  function bootscore_comment_button($args) {
    $args['class_submit'] = 'btn btn-outline-primary'; // since WP 4.1

    return $args;
  }

  add_filter('comment_form_defaults', 'bootscore_comment_button');
endif;
