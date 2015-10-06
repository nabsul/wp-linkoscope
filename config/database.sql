CREATE TABLE link_likes (id PRIMARY KEY, post_id INTEGER, user_id INTEGER);
CREATE TABLE link_scores (id PRIMARY KEY, post_id INTEGER, score INTEGER);
CREATE TABLE comment_likes (id PRIMARY KEY, comment_id INTEGER, user_id INTEGER);
CREATE TABLE comment_scores (id PRIMARY KEY, comment_id INTEGER, score INTEGER);

CREATE INDEX idx_ll_post ON link_likes (post_id);
CREATE INDEX idx_ll_post_likes ON link_likes (post_id, user_id);
CREATE INDEX idx_ls_post ON link_scores (post_id);
CREATE INDEX idx_ls_score ON link_scores (score);

CREATE INDEX idx_cl_post ON comment_likes (comment_id);
CREATE INDEX idx_cl_post_likes ON comment_likes (comment_id, user_id);
CREATE INDEX idx_cs_post ON comment_scores (comment_id);
CREATE INDEX idx_cs_score ON comment_scores (score);
