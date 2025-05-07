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
export type UserLocationResponseData = {
lat: number | null;
lon: number | null;
city: string | null;
state: string | null;
country: string | null;
};
export type VehicleData = {
plate: string;
model: string;
color: string;
};
}
declare namespace App.Data.Parking {
export type CalculateParkingFeeRequestData = {
record_id: string;
exit_time: string;
};
export type CalculateParkingFeeResponseData = {
total_amount: number;
formatted_amount: string;
already_paid: boolean;
};
export type GetNearbyParkingResponseData = {
id: string;
name: string;
address: string;
distance: string;
available_spaces: number;
occupancy_percentage: number;
};
export type GetParkedVehicleResponseData = {
id: string;
plate: string;
brand: string;
model: string;
color: string;
entry_time: string;
};
}
