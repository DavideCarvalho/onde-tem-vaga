import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from 'recharts';
import { ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { cn, formatCurrency } from '@/lib/utils';
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

const chartConfig = {
    value: {
        label: 'Ganhos',
        color: 'hsl(var(--chart-1))',
    },
};

function DayChart({ data }: { data: { hour: number; value: number }[] }) {
    return (
        <ChartContainer config={chartConfig}>
            <AreaChart
                data={data}
                margin={{ left: 12, right: 12 }}
            >
                <CartesianGrid vertical={false} />
                <XAxis
                    dataKey="hour"
                    tickLine={false}
                    axisLine={false}
                    tickMargin={8}
                    tickFormatter={formatHour}
                />
                <YAxis
                    tickFormatter={formatCurrency}
                    width={80}
                    axisLine={false}
                    tickLine={false}
                />
                <ChartTooltip
                    cursor={false}
                    content={<ChartTooltipContent indicator="dot" />}
                />
                <Area
                    dataKey="value"
                    type="natural"
                    fill="var(--color-primary)"
                    fillOpacity={0.4}
                    stroke="var(--color-primary)"
                />
            </AreaChart>
        </ChartContainer>
    );
}

function WeekChart({ data }: { data: { day: string; value: number }[] }) {
    return (
        <ChartContainer config={chartConfig}>
            <AreaChart
                data={data}
                margin={{ left: 12, right: 12 }}
            >
                <CartesianGrid vertical={false} />
                <XAxis
                    dataKey="day"
                    tickLine={false}
                    axisLine={false}
                    tickMargin={8}
                    tickFormatter={formatDate}
                />
                <YAxis
                    tickFormatter={formatCurrency}
                    width={80}
                    axisLine={false}
                    tickLine={false}
                />
                <ChartTooltip
                    cursor={false}
                    content={<ChartTooltipContent indicator="dot" />}
                />
                <Area
                    dataKey="value"
                    type="natural"
                    fill="var(--color-primary)"
                    fillOpacity={0.4}
                    stroke="var(--color-primary)"
                />
            </AreaChart>
        </ChartContainer>
    );
}

function MonthChart({ data }: { data: { day: string; value: number }[] }) {
    return (
        <ChartContainer config={chartConfig}>
            <AreaChart
                data={data}
                margin={{ left: 12, right: 12 }}
            >
                <CartesianGrid vertical={false} />
                <XAxis
                    dataKey="day"
                    tickLine={false}
                    axisLine={false}
                    tickMargin={8}
                    tickFormatter={formatDate}
                />
                <YAxis
                    tickFormatter={formatCurrency}
                    width={80}
                    axisLine={false}
                    tickLine={false}
                />
                <ChartTooltip
                    cursor={false}
                    content={<ChartTooltipContent indicator="dot" />}
                />
                <Area
                    dataKey="value"
                    type="natural"
                    fill="var(--color-primary)"
                    fillOpacity={0.4}
                    stroke="var(--color-primary)"
                />
            </AreaChart>
        </ChartContainer>
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
    const currentIndex = periodsRef.current.indexOf(period);

    const isChartDataEmpty =
        (period === 'day' && dayChartData.length === 0) ||
        (period === 'week' && weekChartData.length === 0) ||
        (period === 'month' && monthChartData.length === 0);

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
                            if (currentIndex === 0) return;
                            const previousIndex = currentIndex - 1;
                            setPeriod(periodsRef.current[previousIndex]);
                        }}
                        className={cn(currentIndex === 0 && 'opacity-50')}
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
                            if (currentIndex === periodsRef.current.length - 1) return;
                            const nextIndex = currentIndex + 1;
                            setPeriod(periodsRef.current[nextIndex]);
                        }}
                        className={cn(currentIndex === periodsRef.current.length - 1 && 'opacity-50')}
                    >
                        <ChevronRight className="h-4 w-4" />
                    </Button>
                </div>
            </div>
            <div className="h-[200px]">
                {isChartDataEmpty ? (
                    <div className="flex items-center justify-center h-full text-muted-foreground">
                        Sem dados para exibir o gráfico.
                    </div>
                ) : (
                    <>
                        {period === 'day' && <DayChart data={dayChartData} />}
                        {period === 'week' && <WeekChart data={weekChartData} />}
                        {period === 'month' && <MonthChart data={monthChartData} />}
                    </>
                )}
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
