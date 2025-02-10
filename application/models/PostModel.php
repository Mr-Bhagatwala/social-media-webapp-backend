



<?php

class PostModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }




    //user;s all post 

    public function getAllPostOfUSer($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('posts'); // Assuming 'posts' is your table name
    
        if ($query->num_rows() > 0) {
            return $query->result_array(); // Return all posts as an associative array
        } else {
            return false; // Return false if no posts are found
        }
    }
    
    // Create a new post
    public function createPost($userId, $content, $mediaUrls) {
        $this->db->trans_start();

        // Insert into posts table
        $this->db->insert('posts', ['user_id' => $userId, 'content' => $content]);
        $postId = $this->db->insert_id();

        // Insert into media table
        foreach ($mediaUrls as $url) {
            $this->db->insert('media', ['post_id' => $postId, 'media_url' => $url]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return ['status' => 'success', 'message' => 'Post created successfully' ,  'post_id' =>  $postId];
        } else {
            return ['status' => 'failed', 'message' => 'Failed to create post'];
        }
    }


    public function tagUserInPost($postId, $taggedUserId, $taggedBy)
    {
        $data = [
            'post_id' => $postId,
            'tagged_user_id' => $taggedUserId,
            'tagged_by' => $taggedBy,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('post_tags', $data);
    }

  


    // Delete a post
    public function deletePost($postId) {
        
        //     $this->db->where('post_id', $postId);
        //    // $this->db->where('user_id', $user_id);
        //     $res = $this->db->get();
        //     if(!$res){
        //         return ['status' => 'error', 'message'=>'Post not existed || User is not allowed to delete someone other post '];
        //     }

        // deleting media with realted postId . ALthough On delete is applied in SQL
        $this->db->where('post_id', $postId);
        $this->db->delete('media');  

        $this->db->where('post_id', $postId);   
        // $this->db->where('user_id', $userId);    
       return $this->db->delete('posts');
      

     
    }

    // Get feed with pagination
    public function getFeed($offset, $sort) {
        $this->db->select('p.post_id, p.content, p.created_at, p.likesCount, p.commentCount, u.id,u.name, u.profile_photo, GROUP_CONCAT(m.media_url) as media');
        $this->db->from('posts p');
        $this->db->join('users u', 'u.id = p.user_id');
        $this->db->join('media m', 'm.post_id = p.post_id', 'left');
        $this->db->group_by('p.post_id');

        if ($sort === 'recent') {
            $this->db->order_by('p.created_at', 'DESC');
        } else {
            $this->db->order_by('p.created_at', 'ASC');
        }

        $this->db->limit(3, $offset);
        return $this->db->get()->result_array();
    }

    public function likePost($postId, $userId) {
        // Insert into likes table
        $this->db->insert('likes', ['post_id' => $postId, 'user_id' => $userId]);
    
        // Check if the like was successfully added
        if ($this->db->affected_rows() > 0) {
            // Update the likesCount in the posts table
            $this->db->set('likesCount', 'likesCount + 1', FALSE);  // FALSE to prevent escaping the SQL expression
            $this->db->where('post_id', $postId);  
            $this->db->update('posts');
    
            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {
                return ['status' => 'success', 'message' => 'Post liked and likes count updated'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update likes count'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Failed to like post'];
        }
    }
    
    public function getLikedPostsByUser($user_id) {
        $this->db->select('post_id');
        $this->db->from('likes');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
    
        // Return the post IDs the user has liked
        return array_column($query->result_array(), 'post_id');
    }

    
    // public function removelike($post_id, $user_id){
    //     $this->db->delete('likes', ['post_id' => $post_id, 'user_id' => $user_id]);
    //     return ['status' => 'success', 'message' => 'removed'];
    // }
    public function removelike($post_id, $user_id) {
        // Delete from likes table
        $this->db->delete('likes', ['post_id' => $post_id, 'user_id' => $user_id]);
    
        // Check if the like was successfully removed
        if ($this->db->affected_rows() > 0) {
            // Decrement the likesCount in the posts table
            $this->db->set('likesCount', 'likesCount - 1', FALSE);  // FALSE to prevent escaping the SQL expression
            $this->db->where('post_id', $post_id);  // Assuming 'id' is the primary key of the posts table
            $this->db->update('posts');
    
            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {
                return ['status' => 'success', 'message' => 'Like removed and likes count updated'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update likes count after removing like'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Failed to remove like'];
        }
    }
    
  
    // Add a comment
    // public function addComment($commentData) {
    //     $this->db->insert('comments', $commentData);
    //     if ($this->db->affected_rows() > 0) {
    //         return ['status' => 'success', 'message' => 'Comment added successfully'];
    //     } else {
    //         return ['status' => 'error', 'message' => 'Failed to add comment'];
    //     }
    // }
    public function addComment($commentData) {
        // Insert the comment into the comments table
        $this->db->insert('comments', $commentData);
    
        // Check if the comment was successfully added
        if ($this->db->affected_rows() > 0) {
            // Increment the commentCount in the posts table
            $this->db->set('commentCount', 'commentCount + 1', FALSE);  // FALSE to prevent escaping the SQL expression
            $this->db->where('post_id', $commentData['post_id']);  // Assuming 'id' is the primary key of the posts table
            $this->db->update('posts');
    
            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {
                return ['status' => 'success', 'message' => 'Comment added and comment count updated'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update comment count after adding comment'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Failed to add comment'];
        }
    }
    

    public function getCommentsofPost($postId) {
        $this->db->select('comments.*, users.name as user_name, users.profile_photo');
        $this->db->from('comments');
        $this->db->join('users', 'comments.user_id = users.id', 'left'); // Adjust 'users.id' to your actual user ID column name
        $this->db->where('comments.post_id', $postId);
        return $this->db->get()->result_array();
    }
    // Get notifications
    public function getNotifications($userId) {
        $this->db->select('message, link, is_read, created_at');
        $this->db->where('user_id', $userId);
        return $this->db->get('notifications')->result_array();
    }
    // get no of likes with post id

    public function getLikesCount($postId){
        $this->db->where('post_id', $postId);
        $this->db->from('likes');
        return $this->db->count_all_results();
    }
    // get no of commnet with post id 

    public function getCommentsCount($postId){
        $this->db->where('post_id', $postId);
        $this->db->from('comments');
        return $this->db->count_all_results();
    }
    //get user posts
    public function isLikeByUser($postId, $userId){
        $this->db->where('post_id', $postId);
        $this->db->where('user_id', $userId);
        
        return $this->db->get('likes')->row();
    }

    public function getUserPost($userId){
        // $this->db->where('user_id', $userId);

        // return $this->db->get('posts')->result_array();
        $this->db->select('p.post_id, p.content, p.created_at,p.likesCount, p.commentCount, u.profile_photo, u.name, u.id, GROUP_CONCAT(m.media_url) as media');
        $this->db->from('posts p');
        
        $this->db->join('users u', 'u.id = p.user_id');
        $this->db->join('media m', 'm.post_id = p.post_id', 'left');
       
        $this->db->group_by('p.post_id');
        $this->db->where('u.id', $userId);

        return $this->db->get()->result_array();
    }
}
?>

