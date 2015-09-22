<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:25 AM
 */

namespace automattic\Rest;

use automattic\Rest\Models\Link;
use automattic\Rest\Models\Comment;

interface iWpApi {
    public function getConfig();
    public function authorize($returnUrl);
    public function access($token, $verifier);
    public function getLinks();
    public function getLink($id);
    public function addLink(Link $link);
    public function updateLink(Link $link);
    public function upVoteLink($id);
    public function downVoteLink($id);
    public function upVoteComment($id);
    public function downVoteComment($id);
    public function deleteLink($id);
    public function deleteComment($id);
    public function getTypes();
    public function getAccount();
    public function getComments($postId);
    public function addComment(Comment $comment);
}
