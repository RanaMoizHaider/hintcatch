import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/CommentController';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/hooks/use-initials';
import { SharedData } from '@/types';
import { Comment } from '@/types/models';
import { router, usePage } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { MessageSquare, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { VoteButton } from './vote-button';

interface CommentItemProps {
    comment: Comment;
    commentableType: string;
    commentableId: number;
}

export function CommentItem({
    comment,
    commentableType,
    commentableId,
}: CommentItemProps) {
    const { auth } = usePage<SharedData>().props;
    const initials = useInitials();

    const [isEditing, setIsEditing] = useState(false);
    const [editBody, setEditBody] = useState(comment.body);
    const [isReplying, setIsReplying] = useState(false);
    const [replyBody, setReplyBody] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const isAuthor = auth.user && comment.user_id === auth.user.id;

    const handleUpdate = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editBody.trim()) return;

        setIsSubmitting(true);
        router.post(
            update.url(comment.id),
            {
                _method: 'PATCH',
                body: editBody,
            },
            {
                preserveScroll: true,
                onSuccess: () => setIsEditing(false),
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    const handleDelete = () => {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        router.delete(destroy.url(comment.id), {
            preserveScroll: true,
        });
    };

    const handleReply = (e: React.FormEvent) => {
        e.preventDefault();
        if (!replyBody.trim()) return;

        setIsSubmitting(true);
        router.post(
            store.url(),
            {
                commentable_type: commentableType,
                commentable_id: commentableId,
                parent_id: comment.id,
                body: replyBody,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsReplying(false);
                    setReplyBody('');
                },
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    return (
        <div className="flex gap-4">
            <div className="flex flex-col items-center gap-2">
                <Avatar className="h-8 w-8 border border-ds-border">
                    <AvatarImage
                        src={comment.user?.avatar || undefined}
                        alt={comment.user?.name}
                    />
                    <AvatarFallback>
                        {initials(comment.user?.name || 'User')}
                    </AvatarFallback>
                </Avatar>
                <VoteButton
                    votableType="comment"
                    votableId={comment.id}
                    voteScore={comment.vote_score}
                    userVote={comment.user_vote}
                    size="sm"
                />
            </div>

            <div className="min-w-0 flex-1">
                <div className="mb-1 flex items-center gap-2">
                    <span className="text-sm font-bold text-ds-text-primary">
                        {comment.user?.name}
                    </span>
                    <span className="text-xs text-ds-text-muted">
                        {formatDistanceToNow(new Date(comment.created_at), {
                            addSuffix: true,
                        })}
                    </span>
                    {comment.is_edited && (
                        <span className="text-xs text-ds-text-muted italic">
                            (edited)
                        </span>
                    )}
                </div>

                {isEditing ? (
                    <form onSubmit={handleUpdate} className="mt-2">
                        <textarea
                            value={editBody}
                            onChange={(e) => setEditBody(e.target.value)}
                            className="mb-2 min-h-[80px] w-full border-2 border-ds-border bg-ds-bg-base p-2 text-sm text-ds-text-primary focus:border-ds-text-primary focus:outline-hidden"
                            required
                        />
                        <div className="flex justify-end gap-2">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => setIsEditing(false)}
                                disabled={isSubmitting}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                size="sm"
                                disabled={isSubmitting || !editBody.trim()}
                            >
                                {isSubmitting ? 'Saving...' : 'Save'}
                            </Button>
                        </div>
                    </form>
                ) : (
                    <div className="text-sm break-words whitespace-pre-wrap text-ds-text-secondary">
                        {comment.body}
                    </div>
                )}

                <div className="mt-2 flex items-center gap-4">
                    {auth.user && (
                        <button
                            onClick={() => setIsReplying(!isReplying)}
                            className="flex items-center gap-1 text-xs font-medium text-ds-text-muted transition-colors hover:text-ds-text-primary"
                        >
                            <MessageSquare className="h-3 w-3" />
                            Reply
                        </button>
                    )}

                    {isAuthor && !isEditing && (
                        <>
                            <button
                                onClick={() => {
                                    setEditBody(comment.body);
                                    setIsEditing(true);
                                }}
                                className="flex items-center gap-1 text-xs font-medium text-ds-text-muted transition-colors hover:text-ds-text-primary"
                            >
                                <Pencil className="h-3 w-3" />
                                Edit
                            </button>
                            <button
                                onClick={handleDelete}
                                className="flex items-center gap-1 text-xs font-medium text-ds-text-muted transition-colors hover:text-red-500"
                            >
                                <Trash2 className="h-3 w-3" />
                                Delete
                            </button>
                        </>
                    )}
                </div>

                {isReplying && (
                    <form
                        onSubmit={handleReply}
                        className="mt-4 border-l-2 border-ds-border pl-4"
                    >
                        <textarea
                            value={replyBody}
                            onChange={(e) => setReplyBody(e.target.value)}
                            placeholder="Write a reply..."
                            className="mb-2 min-h-[80px] w-full border-2 border-ds-border bg-ds-bg-base p-2 text-sm text-ds-text-primary focus:border-ds-text-primary focus:outline-hidden"
                            required
                            autoFocus
                        />
                        <div className="flex justify-end gap-2">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => setIsReplying(false)}
                                disabled={isSubmitting}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                size="sm"
                                disabled={isSubmitting || !replyBody.trim()}
                            >
                                {isSubmitting ? 'Posting...' : 'Reply'}
                            </Button>
                        </div>
                    </form>
                )}

                {comment.replies && comment.replies.length > 0 && (
                    <div className="mt-4 space-y-4 border-l-2 border-ds-border pl-4">
                        {comment.replies.map((reply) => (
                            <CommentItem
                                key={reply.id}
                                comment={reply}
                                commentableType={commentableType}
                                commentableId={commentableId}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
