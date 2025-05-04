import axios from 'axios';

export interface CarBrand {
    nome: string;
    valor: string;
}

export interface CarModel {
    modelo: string;
}

export interface PricingOptionsData {
    value: string;
    label: string;
}

export interface RegisterEntryData {
    plate: string;
    brand: string;
    model: string;
    color: string;
    pricing_type: string;
}

export interface RegisterEntryResponse {
    message: string;
    data: {
        id: number;
        plate: string;
        brand: string;
        model: string;
        color: string;
        pricing_type: string;
        created_at: string;
    };
}

export interface ParkedVehicle {
    id: number;
    plate: string;
    brand: string;
    model: string;
    color: string;
    entry_time: string;
}

export interface RegisterExitData {
    record_id: string;
    exit_time: string;
}

export interface RegisterExitResponse {
    message: string;
    data: {
        id: number;
        plate: string;
        brand: string;
        model: string;
        color: string;
        pricing_type: string;
        entry_time: string;
        exit_time: string;
        total_time: string;
        total_price: number;
    };
}

const brasilApi = axios.create({
    baseURL: 'https://brasilapi.com.br/api',
});

export async function fetchCarBrands(): Promise<CarBrand[]> {
    const { data } = await brasilApi.get<CarBrand[]>('/fipe/marcas/v1/carros');
    return data;
}

export async function fetchCarModels(brandCode: string): Promise<CarModel[]> {
    const { data } = await brasilApi.get<CarModel[]>(`/fipe/veiculos/v1/carros/${brandCode}`);
    return data;
}

export async function fetchPricingOptions(): Promise<PricingOptionsData[]> {
    const response = await axios.get(route('api.pricing-options'));
    return response.data;
}

export async function registerEntry(data: RegisterEntryData): Promise<RegisterEntryResponse> {
    const response = await axios.post(route('api.parking.entry'), data);
    return response.data;
}

export async function registerExit(data: RegisterExitData): Promise<RegisterExitResponse> {
    const response = await axios.post(route('api.parking.exit'), data);
    return response.data;
}

export async function getParkedVehicles(): Promise<ParkedVehicle[]> {
    const response = await axios.get<ParkedVehicle[]>(route('api.parking.parked-vehicles'));
    return response.data;
}

export async function calculateParkingFee(
    data: App.Data.Parking.CalculateParkingFeeRequestData,
): Promise<App.Data.Parking.CalculateParkingFeeResponseData> {
    const response = await axios.post(route('api.parking.calculate-fee'), data);
    return response.data;
}
