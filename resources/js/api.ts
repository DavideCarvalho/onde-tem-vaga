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

export interface NearbyParking {
    id: number;
    name: string;
    distance: string;
    address: string;
}

export interface PhotonSuggestion {
    name: string;
    city?: string;
    state?: string;
    country?: string;
    lat: number;
    lon: number;
    label: string;
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
    const response = await axios.post(route('api.parking.exit', {
        record: data.record_id,
    }), data);
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

export async function geocodeAddress(address: string): Promise<{ lat: number, lon: number } | null> {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;
    const res = await fetch(url, {
        headers: {
            'Accept-Language': 'pt-BR',
        }
    });
    const data = await res.json();
    if (data && data.length > 0) {
        return { lat: Number.parseFloat(data[0].lat), lon: Number.parseFloat(data[0].lon) };
    }
    return null;
}

export async function getNearbyParkings(lat: number, lng: number): Promise<NearbyParking[]> {
    if (!lat || !lng) return [];
    const response = await axios.get(route('api.parking.nearby'), {
        params: { lat, lng },
    });
    return response.data;
}

export async function fetchPhotonSuggestions(query: string): Promise<PhotonSuggestion[]> {
    if (!query) return [];
    const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(query)}&limit=5`;
    const res = await fetch(url);
    const data = await res.json();
    if (!data.features) return [];
    return data.features.map((f: {
        properties: {
            name: string;
            city?: string;
            state?: string;
            country?: string;
        };
        geometry: { coordinates: [number, number] };
    }) => ({
        name: f.properties.name,
        city: f.properties.city,
        state: f.properties.state,
        country: f.properties.country,
        lat: f.geometry.coordinates[1],
        lon: f.geometry.coordinates[0],
        label: [
            f.properties.name,
            f.properties.city,
            f.properties.state,
            f.properties.country
        ].filter(Boolean).join(', ')
    }));
}
