type AppSkeletonProps = {
    className?: string;
};

export function AppSkeleton({ className = '' }: AppSkeletonProps) {
    return <div className={`animate-pulse rounded-lg bg-slate-200/80 dark:bg-slate-800 ${className}`} />;
}
