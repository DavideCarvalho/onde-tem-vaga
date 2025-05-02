import { Button } from '@/components/ui/button';
import { useSuspenseQuery } from '@tanstack/react-query';
import axios from 'axios';
import { Suspense } from 'react';
import { type FallbackProps, ErrorBoundary } from 'react-error-boundary';

function UsedSpotsCardContent() {
    const { data } = useSuspenseQuery<App.Data.GetUsedSpotsData>({
        queryKey: ['used-spots'],
        queryFn: () => axios.get(route('api.used-spots')).then((res) => res.data),
    });

    const color = data.percentage < 50 ? 'bg-green-500' : data.percentage < 80 ? 'bg-yellow-500' : 'bg-red-500';

    return (
        <div className="flex h-full flex-col items-center justify-center gap-4 p-4">
            <h3 className="text-lg font-semibold">Vagas Ocupadas</h3>
            <div className="flex flex-col items-center gap-2">
                <div className="text-4xl font-bold">{data.percentage.toFixed(0)}%</div>
                <div className="text-sm text-gray-500">
                    {data.usedSpots} de {data.totalSpots} vagas
                </div>
                <div className="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                    <div className={`h-full ${color} transition-all duration-500`} style={{ width: `${data.percentage}%` }} />
                </div>
            </div>
        </div>
    );
}

function ErrorFallback({ resetErrorBoundary }: FallbackProps) {
    return (
        <div className="flex h-full flex-col items-center justify-center gap-2 p-4 text-center">
            <p className="text-red-500">Erro ao carregar vagas ocupadas</p>
            <Button onClick={resetErrorBoundary} className="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                Tentar novamente
            </Button>
        </div>
    );
}

function LoadingFallback() {
    return (
        <div className="flex h-full items-center justify-center p-4">
            <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent" />
        </div>
    );
}

export default function UsedSpotsCard() {
    return (
        <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
            <ErrorBoundary FallbackComponent={ErrorFallback}>
                <Suspense fallback={<LoadingFallback />}>
                    <UsedSpotsCardContent />
                </Suspense>
            </ErrorBoundary>
        </div>
    );
}
