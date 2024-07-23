<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = $this->comments;
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResoure($this->user),
            'group' => new GroupResource($this->group),
            // 'attachments' => $this->attachments function belongsto hasmany ben model,
            'attachments' => PostAttachmentResource::collection($this->attachments),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::convertCommentsIntoTree($comments)
        ];
    }

    private static function convertCommentsIntoTree($comments, $parentId = null): array
    {
        $commentTree = [];

        // $comment = App/Models/Comment
        foreach ($comments as $comment) {
            // Tìm kiếm những comment có giá trị defalut = null => là comment gốc
            if ($comment->parent_id === $parentId) {
                // Find all comment which has parentId as $comment->id
                $children = self::convertCommentsIntoTree($comments, $comment->id);
                // childComments Comment Model
                $comment->childComments = $children;
                // collect($children)->sum('numOfComments') return from model variable
                $comment->numOfComments =
                    collect($children)
                    ->sum('numOfComments') + count($children);

                $commentTree[] = new CommentResource($comment);
            }
        }

        return $commentTree;
    }
}
