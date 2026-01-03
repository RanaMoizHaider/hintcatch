import { show as showSkill } from '@/actions/App/Http/Controllers/SkillController';
import { Badge } from '@/components/ui/badge';
import type { Skill } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp } from 'lucide-react';

interface SkillCardProps {
    skill: Skill;
}

export function SkillCard({ skill }: SkillCardProps) {
    return (
        <Link
            href={showSkill(skill.slug)}
            className="group flex flex-col border-2 border-ds-bg-card bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                        {skill.name}
                    </h3>
                </div>
                <div className="flex items-center gap-1 text-xs text-ds-text-muted">
                    <ArrowUp className="h-3 w-3" />
                    <span>{skill.vote_score}</span>
                </div>
            </div>

            {skill.description && (
                <p className="mt-2 line-clamp-2 text-xs text-ds-text-secondary">
                    {skill.description}
                </p>
            )}

            <div className="mt-auto flex items-center gap-2 pt-3">
                {skill.category && (
                    <Badge
                        variant="outline"
                        className="border-ds-border text-ds-text-muted"
                    >
                        {skill.category.name}
                    </Badge>
                )}
                {skill.license && (
                    <Badge
                        variant="outline"
                        className="border-ds-border text-ds-text-muted"
                    >
                        {skill.license}
                    </Badge>
                )}
            </div>
        </Link>
    );
}
