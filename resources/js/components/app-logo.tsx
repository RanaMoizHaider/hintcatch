import Icons from '@/components/ui/icons';

export default function AppLogo() {
    return (
        <>
            <Icons.logo className="size-6 sm:size-7" />
            <span className="hidden text-xl font-medium text-white sm:inline">
                HintCatch
            </span>
        </>
    );
}
