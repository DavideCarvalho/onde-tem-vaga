import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import { useQuery } from '@tanstack/react-query';
import { useDebounce } from '@uidotdev/usehooks';
import { getNearbyParkings, geocodeAddress, fetchPhotonSuggestions } from '../api';
import { useForm } from 'react-hook-form';
import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import type { PhotonSuggestion } from '../api';
import { MapPin, Search, Building2 } from 'lucide-react';

const schema = z.object({
    address: z.string().min(1, 'Digite um endereço'),
});

type FormData = z.infer<typeof schema>;

type Coords = { lat: number; lon: number } | null;

export default function FindParking() {
    const [geoError, setGeoError] = useState<string | null>(null);
    const [autoCoords, setAutoCoords] = useState<Coords>(null);
    const [suggestions, setSuggestions] = useState<PhotonSuggestion[]>([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const [justSelectedSuggestion, setJustSelectedSuggestion] = useState(false);
    const [isInputFocused, setIsInputFocused] = useState(false);

    const { register, watch, formState: { errors }, setValue } = useForm<FormData>({
        resolver: zodResolver(schema),
        mode: 'onChange',
        defaultValues: { address: '' },
    });
    const address = watch('address');
    const debouncedAddress = useDebounce(address, 500);

    // Query para geocoding
    const {
        data: coords,
        isFetching: isFetchingCoords,
        error: coordsError,
    } = useQuery<Coords, Error>({
        queryKey: ['geocode', debouncedAddress],
        queryFn: () => debouncedAddress ? geocodeAddress(debouncedAddress) : null,
        enabled: !!debouncedAddress,
    });

    // Buscar sugestões do Photon
    useEffect(() => {
        if (justSelectedSuggestion) {
            setJustSelectedSuggestion(false);
            return;
        }
        if (debouncedAddress) {
            fetchPhotonSuggestions(debouncedAddress).then(setSuggestions);
            setShowSuggestions(true);
        } else {
            setSuggestions([]);
            setShowSuggestions(false);
        }
    }, [debouncedAddress]);

    // Ao selecionar sugestão
    function handleSelectSuggestion(s: PhotonSuggestion) {
        setShowSuggestions(false);
        setSuggestions([]);
        setValue('address', s.label, { shouldValidate: true });
        setSelectedCoords({ lat: s.lat, lon: s.lon });
        setJustSelectedSuggestion(true);
    }

    // Permitir sobrescrever coords manualmente
    const [selectedCoords, setSelectedCoords] = useState<Coords>(null);
    useEffect(() => {
        if (!debouncedAddress) {
            setSelectedCoords(null);
        }
    }, [debouncedAddress]);

    // Atualiza geoError conforme resultado do geocoding
    useEffect(() => {
        if (!debouncedAddress) {
            setGeoError(null);
        } else if (coordsError) {
            setGeoError('Endereço não encontrado.');
        } else if (debouncedAddress && coords === null) {
            setGeoError('Endereço não encontrado.');
        } else {
            setGeoError(null);
        }
    }, [debouncedAddress, coords, coordsError]);

    // Busca localização por IP ao montar
    useEffect(() => {
        fetch('/api/user-location')
            .then(res => res.json())
            .then(data => {
                if (data.lat && data.lon) setAutoCoords({ lat: data.lat, lon: data.lon });
            });
    }, []);

    // Use autoCoords se não tiver endereço digitado ou coords selecionado
    const coordsToUse = selectedCoords || autoCoords;

    // Query para estacionamentos próximos
    const { data: results = [], isFetching } = useQuery({
        queryKey: ['nearby-parkings', coordsToUse],
        queryFn: () => coordsToUse && typeof coordsToUse.lat === 'number' && typeof coordsToUse.lon === 'number'
            ? getNearbyParkings(coordsToUse.lat, coordsToUse.lon)
            : [],
        enabled: !!coordsToUse && typeof coordsToUse.lat === 'number' && typeof coordsToUse.lon === 'number',
    });

    return (
        <>
            <Head title="Buscar Estacionamento" />
            <div className="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-[#0a0a0a] to-[#232323] dark:from-[#0a0a0a] dark:to-[#232323] p-4">
                <div className="w-full max-w-lg bg-white/90 dark:bg-[#18181b]/90 rounded-2xl shadow-2xl p-8 flex flex-col items-center gap-6 backdrop-blur-md">
                    <div className="flex flex-col items-center gap-2">
                        <Building2 className="h-10 w-10 text-[#f53003] mb-1" />
                        <h1 className="text-3xl font-extrabold text-center text-[#1b1b18] dark:text-[#EDEDEC]">Onde tem vaga?</h1>
                        <p className="text-base text-gray-600 dark:text-gray-400 text-center">Encontre estacionamentos próximos de você ou de um endereço</p>
                    </div>
                    <form className="w-full flex flex-col gap-2 relative" autoComplete="off" onSubmit={e => e.preventDefault()}>
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                            <input
                                type="text"
                                className={`w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 dark:border-[#232323] bg-white dark:bg-[#232323] text-[#1b1b18] dark:text-white shadow focus:ring-2 focus:ring-[#f53003] focus:border-[#f53003] transition placeholder:text-gray-400 ${errors.address ? 'border-red-500 ring-red-500' : ''}`}
                                placeholder="Digite um endereço..."
                                {...register('address')}
                                required
                                autoComplete="off"
                                onFocus={() => { setShowSuggestions(true); setIsInputFocused(true); }}
                                onBlur={() => { setShowSuggestions(false); setIsInputFocused(false); }}
                            />
                            {isInputFocused && showSuggestions && suggestions.length > 0 && (
                                <ul className="absolute left-0 right-0 mt-2 bg-white dark:bg-[#232323] border border-gray-200 dark:border-[#232323] z-20 max-h-56 overflow-y-auto rounded-lg shadow-lg animate-fade-in">
                                    {suggestions.map(s => (
                                        <li key={`${s.label}-${s.lat}-${s.lon}`} className="p-0 m-0 border-none bg-transparent">
                                            <button
                                                type="button"
                                                className="w-full text-left p-3 hover:bg-[#f53003]/10 dark:hover:bg-[#f53003]/20 transition-colors text-[#1b1b18] dark:text-white"
                                                onMouseDown={() => handleSelectSuggestion(s)}
                                                onKeyDown={e => {
                                                    if (e.key === 'Enter' || e.key === ' ') handleSelectSuggestion(s);
                                                }}
                                            >
                                                <span className="font-medium">{s.name}</span>
                                                <span className="block text-xs text-gray-500 dark:text-gray-400">{s.label.replace(s.name + ',', '').trim()}</span>
                                            </button>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                        {errors.address && (
                            <span className="text-sm text-red-500 mt-1">{errors.address.message}</span>
                        )}
                    </form>
                    {geoError && (
                        <div className="text-sm text-red-500 mb-2">{geoError}</div>
                    )}
                    {isFetchingCoords && debouncedAddress && (
                        <div className="flex items-center gap-2 text-sm text-gray-500 mb-2 animate-pulse">
                            <Search className="h-4 w-4 animate-spin" /> Buscando coordenadas...
                        </div>
                    )}
                    {isFetching && coordsToUse && (
                        <div className="flex items-center gap-2 text-sm text-gray-500 mb-2 animate-pulse">
                            <MapPin className="h-4 w-4 animate-bounce" /> Buscando estacionamentos...
                        </div>
                    )}
                    {Array.isArray(results) && results.length > 0 && (
                        <div className="w-full flex flex-col gap-3 mt-2">
                            <h2 className="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-1 flex items-center gap-2">
                                <MapPin className="h-5 w-5 text-[#f53003]" /> Estacionamentos próximos
                            </h2>
                            <ul className="flex flex-col gap-3">
                                {results.map(est => (
                                    <li key={est.id} className="rounded-xl border border-gray-200 dark:border-[#232323] bg-white dark:bg-[#232323] p-4 shadow flex flex-col gap-1 transition hover:shadow-lg">
                                        <span className="font-bold text-[#1b1b18] dark:text-white text-base flex items-center gap-2">
                                            <Building2 className="h-5 w-5 text-[#f53003]" /> {est.name}
                                        </span>
                                        <span className="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                            <MapPin className="h-4 w-4 text-gray-400" /> {est.address}
                                        </span>
                                        <span className="text-xs text-gray-500 dark:text-gray-400">{est.distance} de distância</span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                    {coordsToUse && !isFetching && Array.isArray(results) && results.length === 0 && !geoError && (
                        <div className="text-sm text-gray-500 mt-4">Nenhum estacionamento encontrado.</div>
                    )}
                </div>
                <style>{`
                    .animate-fade-in { animation: fadeIn 0.2s ease; }
                    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
                `}</style>
            </div>
        </>
    );
} 