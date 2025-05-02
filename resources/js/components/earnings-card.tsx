import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Area, CartesianGrid, Chart, Tooltip, XAxis, YAxis } from '@/components/ui/charts';
import { formatCurrency } from '@/lib/utils';
import { useSuspenseQuery } from '@tanstack/react-query';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Suspense, useRef, useState } from 'react';
import { ErrorBoundary } from 'react-error-boundary';

type Period = 'day' | 'week' | 'month';

const dateFormatter = new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
});

const hourFormatter = new Intl.DateTimeFormat('pt-BR', {
    hour: '2-digit',
    hour12: false,
});

function formatHour(hour: number) {
    return hourFormatter.format(new Date().setHours(hour));
}

function formatDate(date: string) {
    try {
        return dateFormatter.format(new Date(date));
    } catch {
        return date;
    }
}

function DayChart({ data }: { data: { hour: number; value: number }[] }) {
    return (
        <Chart data={data}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis
                dataKey="hour"
                tickFormatter={formatHour}
            />
            <YAxis tickFormatter={formatCurrency} width={80} />
            <Tooltip
                formatter={(value: number) => [formatCurrency(value), 'Ganhos']}
                labelFormatter={(label: number) => formatHour(label)}
            />
            <Area type="monotone" dataKey="value" stroke="#8884d8" fill="#8884d8" fillOpacity={0.3} />
        </Chart>
    );
}

function WeekChart({ data }: { data: { day: string; value: number }[] }) {
    return (
        <Chart data={data}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis
                dataKey="day"
                tickFormatter={formatDate}
            />
            <YAxis tickFormatter={formatCurrency} width={80} />
            <Tooltip
                formatter={(value: number) => [formatCurrency(value), 'Ganhos']}
                labelFormatter={(label: string) => formatDate(label)}
            />
            <Area type="monotone" dataKey="value" stroke="#8884d8" fill="#8884d8" fillOpacity={0.3} />
        </Chart>
    );
}

function MonthChart({ data }: { data: { day: string; value: number }[] }) {
    return (
        <Chart data={data}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis
                dataKey="day"
                tickFormatter={formatDate}
            />
            <YAxis tickFormatter={formatCurrency} width={80} />
            <Tooltip
                formatter={(value: number) => [formatCurrency(value), 'Ganhos']}
                labelFormatter={(label: string) => formatDate(label)}
            />
            <Area type="monotone" dataKey="value" stroke="#8884d8" fill="#8884d8" fillOpacity={0.3} />
        </Chart>
    );
}

function EarningsContent() {
    const [period, setPeriod] = useState<Period>('day');
    const periodsRef = useRef<Period[]>(['day', 'week', 'month']);
    const { data } = useSuspenseQuery<App.Data.GetEarningsData>({
        queryKey: ['earnings'],
        queryFn: async () => {
            const response = await fetch(route('api.earnings'));
            return response.json();
        },
    });

    const total = period === 'day' ? data?.today : period === 'week' ? data?.week : data?.month;
    const dayChartData = data?.chart.day ?? [];
    const weekChartData = data?.chart.week ?? [];
    const monthChartData = data?.chart.month ?? [];

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <div className="space-y-1">
                    <p className="text-muted-foreground text-sm">Ganhos Totais</p>
                    <p className="text-2xl font-bold">{formatCurrency(total ?? 0)}</p>
                </div>
                <div className="flex items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => {
                            const currentIndex = periodsRef.current.indexOf(period);
                            if (currentIndex === 0) return;
                            const previousIndex = currentIndex - 1;
                            setPeriod(periodsRef.current[previousIndex]);
                        }}
                    >
                        <ChevronLeft className="h-4 w-4" />
                    </Button>
                    <div className="w-24 text-center">
                        {period === 'day' && 'Hoje'}
                        {period === 'week' && 'Esta Semana'}
                        {period === 'month' && 'Este Mês'}
                    </div>
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => {
                            const currentIndex = periodsRef.current.indexOf(period);
                            if (currentIndex === periodsRef.current.length - 1) return;
                            const nextIndex = currentIndex + 1;
                            setPeriod(periodsRef.current[nextIndex]);
                        }}
                    >
                        <ChevronRight className="h-4 w-4" />
                    </Button>
                </div>
            </div>
            <div className="h-[200px]">
                {period === 'day' && <DayChart data={dayChartData} />}
                {period === 'week' && <WeekChart data={weekChartData} />}
                {period === 'month' && <MonthChart data={monthChartData} />}
            </div>
        </div>
    );
}

function EarningsSkeleton() {
    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <div className="space-y-1">
                    <div className="bg-muted h-4 w-24 rounded" />
                    <div className="bg-muted h-8 w-32 rounded" />
                </div>
                <div className="flex items-center gap-2">
                    <div className="bg-muted h-8 w-8 rounded" />
                    <div className="bg-muted h-8 w-24 rounded" />
                    <div className="bg-muted h-8 w-8 rounded" />
                </div>
            </div>
            <div className="bg-muted h-[200px] rounded" />
        </div>
    );
}

function EarningsError() {
    return <div className="text-destructive text-sm">Erro ao carregar ganhos</div>;
}

export default function EarningsCard() {
    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Ganhos</CardTitle>
            </CardHeader>
            <CardContent>
                <ErrorBoundary FallbackComponent={EarningsError}>
                    <Suspense fallback={<EarningsSkeleton />}>
                        <EarningsContent />
                    </Suspense>
                </ErrorBoundary>
            </CardContent>
        </Card>
    );
}
