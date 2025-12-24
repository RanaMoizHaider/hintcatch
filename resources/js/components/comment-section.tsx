import { store } from '@/actions/App/Http/Controllers/CommentController';
import { Button } from '@/components/ui/button';
import { SharedData } from '@/types';
import { Comment } from '@/types/models';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { CommentItem } from './comment-item';

interface CommentSectionProps {
    commentableType: 'config' | 'prompt' | 'mcp-server';
    commentableId: number;
    comments: Comment[];
    className?: string;
}

export function CommentSection({
    commentableType,
    commentableId,
    comments,
    className,
}: CommentSectionProps) {
    const { auth } = usePage<SharedData>().props;
    const [body, setBody] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!body.trim()) return;

        setIsSubmitting(true);
        router.post(
            store.url(),
            {
                commentable_type: commentableType,
                commentable_id: commentableId,
                body: body,
            },
            {
                preserveScroll: true,
                onSuccess: () => setBody(''),
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    return (
        <div
            className={`border-2 border-ds-border bg-ds-bg-card p-6 ${className}`}
        >
            <h3 className="mb-6 text-lg font-bold text-ds-text-primary">
                Comments ({comments.length})
            </h3>

            {auth.user ? (
                <form onSubmit={handleSubmit} className="mb-8">
                    <textarea
                        value={body}
                        onChange={(e) => setBody(e.target.value)}
                        placeholder="Add a comment..."
                        className="mb-3 min-h-[100px] w-full border-2 border-ds-border bg-ds-bg-base p-3 text-ds-text-primary placeholder:text-ds-text-muted focus:border-ds-text-primary focus:outline-hidden"
                        required
                    />
                    <div className="flex justify-end">
                        <Button
                            type="submit"
                            disabled={isSubmitting || !body.trim()}
                        >
                            {isSubmitting ? 'Posting...' : 'Post Comment'}
                        </Button>
                    </div>
                </form>
            ) : (
                <div className="mb-8 border border-ds-border bg-ds-bg-secondary p-4 text-center">
                    <p className="mb-2 text-ds-text-secondary">
                        Please log in to join the discussion.
                    </p>
                    <Button
                        variant="outline"
                        onClick={() => router.get('/login')}
                    >
                        Log In
                    </Button>
                </div>
            )}

            <div className="space-y-6">
                {comments.map((comment) => (
                    <CommentItem
                        key={comment.id}
                        comment={comment}
                        commentableType={commentableType}
                        commentableId={commentableId}
                    />
                ))}
            </div>
        </div>
    );
}
