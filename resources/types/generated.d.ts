declare namespace App.Data {
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
