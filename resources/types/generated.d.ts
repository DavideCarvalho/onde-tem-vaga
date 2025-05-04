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
export type GetPricingOptionsData = {
value: string;
label: string;
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
declare namespace App.Data.Parking {
export type CalculateParkingFeeRequestData = {
record_id: number;
exit_time: string;
};
export type CalculateParkingFeeResponseData = {
total_amount: number;
formatted_amount: string;
};
}
