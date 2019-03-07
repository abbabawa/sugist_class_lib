<?php
	
	/**
	* User class to hold functionalities for Forum section of the app
	*Author: Abba Bawa
	*Created: 24/06/2017
	*/
	require_once("DBConnection.php");

	class Forum
	{

		private $memberId;
		private $forumId;
		private $dbconnection;
		
		function __construct($userId)
		{
			if (isset($userId)) {
				$conn = new Database();
				$this->dbconnection = $conn->getConnection();
				$stmt = $this->dbconnection->prepare("SELECT * FROM forum_members WHERE user_id = ?");
				$stmt->execute([$userId]);
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->memberId = $res['member_id'];
				$this->forumId = $res['forum_id'];
			}
		}

		public function getForumId(){
			return $this->forumId;
		}

		public function getMemberId(){
			return $this->memberId;
		}

		public function getForumMembers($startId){
			$stmt = $this->dbconnection->prepare("SELECT f.*, u.f_name, u.l_name, u.user_id FROM forum_members f, user u WHERE f.member_id > ? AND f.forum_id = ? AND f.user_id = u.user_id LIMIT 10");
			$stmt->execute([$startId, $this->forumId]);
			return $stmt;
		}


		public function getForumPosts($startId){
		    //affected by post ids
			$stmt = $this->dbconnection->prepare("SELECT p.*, f.user_id, u.f_name, u.l_name FROM forum_posts p, forum_members f, user u WHERE (p.forum_id = ? AND p.post_id <= ?) AND (p.member_id = f.member_id AND f.user_id = u.user_id) ORDER BY post_date DESC, post_time DESC LIMIT 10");
			$stmt->execute([$this->forumId, $startId]);
			return $stmt;
		}

		public function getMostRecentPostId(){
		    $date = date("y-m-d", strtotime("today"));
			$stmt = $this->dbconnection->prepare("SELECT post_id AS id FROM forum_posts WHERE forum_id = ? ORDER BY post_date DESC, post_time DESC");
			$stmt->execute([$this->forumId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
		}

		public function getPosterName($postId){
			$stmt = $this->dbconnection->prepare("SELECT u.user_id, u.f_name, u.l_name FROM forum_posts p, forum_members m, user u WHERE p.member_id = m.member_id AND m.user_id = u.user_id AND p.post_id = ?");
			$stmt->execute([$postId]);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function postComment($post){
			$postDate = date("y-m-d", strtotime("today"));
			$postTime = $time = date("H:i:s");
			$stmt = $this->dbconnection->prepare("INSERT INTO forum_posts (member_id, post, forum_id, post_date, post_time) VALUES (?, ?, ?, ?, ?)");
			$res = $stmt->execute([$this->memberId, $post, $this->forumId, $postDate, $postTime]);
			return $res;
		}

		public function postPic($pic){
			$postDate = date("y-m-d", strtotime("today"));
			$postTime = $time = date("H:i:s");
			$stmt = $this->dbconnection->prepare("INSERT INTO forum_posts (member_id, pic, forum_id, post_date, post_time) VALUES (?, ?, ?, ?, ?)");
			$res = $stmt->execute([$this->memberId, $pic, $this->forumId, $postDate, $postTime]);
			return $res;
		}

		public function postCommentAndPic($post, $pic){
			$postDate = date("y-m-d", strtotime("today"));
			$postTime = $time = date("H:i:s");
			$stmt = $this->dbconnection->prepare("INSERT INTO forum_posts (member_id, post, pic, forum_id, post_date, post_time) VALUES (?, ?, ?, ?, ?, ?)");
			$res = $stmt->execute([$this->memberId, $post, $pic, $this->forumId, $postDate, $postTime]);
			return $res;
		}

		public function replyToPost($postId, $reply){
			$replyDate = date("y-m-d", strtotime("today"));
			$replyTime = $time = date("H:i:s");
			$stmt = $this->dbconnection->prepare("INSERT INTO forum_post_replies (post_id, reply, member_id, reply_date, reply_time) VALUES (?, ?, ?, ?, ?)");
			$res = $stmt->execute([$postId, $reply, $this->memberId, $replyDate, $replyTime]);

			$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
			$last = $id->fetch(PDO::FETCH_NUM);
			$lastId = $last[0];

			$stmt = $this->dbconnection->prepare("SELECT r.*, f.*, u.f_name, u.l_name FROM forum_post_replies r, forum_members f, user u WHERE r.member_id = f.member_id AND f.user_id = u.user_id AND r.reply_id = ?");
			$stmt->execute([$lastId]);
			return $stmt;
		}

		public function getPostReplies($postId){
			$stmt = $this->dbconnection->prepare("SELECT r.*, f.*, u.f_name, u.l_name FROM forum_post_replies r, forum_members f, user u WHERE r.member_id = f.member_id AND f.user_id = u.user_id AND r.post_id = ?");
			$stmt->execute([$postId]);
			return $stmt;
		}

		public function getNumberOfReplies($postId){
			$stmt = $this->dbconnection->prepare("SELECT COUNT(reply) AS num_of_replies FROM forum_post_replies WHERE post_id = ?");
			$stmt->execute([$postId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['num_of_replies'];
		}

		public function deletePost($postId){
			$stmt = $this->dbconnection->prepare("DELETE FROM forum_posts WHERE post_id = ?");
			$res = $stmt->execute([$postId]);
			return $res;
		}
	}
?>