declare namespace App.Data {
export type EarningsChartData = {
day: Array<any>;
week: Array<any>;
month: Array<any>;
};
export type EarningsChartDayData = {
day: string;
value: number;
};
export type EarningsChartHourData = {
hour: number;
value: number;
};
export type GetEarningsData = {
today: number;
week: number;
month: number;
chart: App.Data.EarningsChartData;
};
export type GetUsedSpotsData = {
totalSpots: number;
usedSpots: number;
percentage: number;
};
export type ParkingSpotData = {
id: number;
identification: string;
entry_time: string | null;
vehicle: App.Data.VehicleData | null;
};
export type VehicleData = {
plate: string;
model: string;
color: string;
};
}
