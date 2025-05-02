import * as React from "react"
import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts"
import type { AreaProps, CartesianGridProps, ResponsiveContainerProps, TooltipProps, XAxisProps, YAxisProps } from "recharts"

interface ChartData {
    [key: string]: string | number
}

interface ChartProps {
    data: ChartData[]
    children: React.ReactNode
}

export function Chart({ data, children }: ChartProps) {
    return (
        <ResponsiveContainer width="100%" height="100%">
            <AreaChart data={data}>
                {children}
            </AreaChart>
        </ResponsiveContainer>
    )
}

export type { AreaProps, CartesianGridProps, ResponsiveContainerProps, TooltipProps, XAxisProps, YAxisProps }
export { Area, CartesianGrid, Tooltip, XAxis, YAxis } 