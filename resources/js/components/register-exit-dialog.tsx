import { calculateParkingFee, getParkedVehicles, registerExit, type ParkedVehicle } from '@/api';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Loader2 } from 'lucide-react';
import { useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import * as z from 'zod';
import toast from 'react-hot-toast';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const formSchema = z.object({
    record_id: z.string().min(1, 'Selecione um veículo'),
});

type FormData = z.infer<typeof formSchema>;

export function RegisterExitDialog() {
    const [isOpen, setIsOpen] = useState(false);
    const [selectedVehicle, setSelectedVehicle] = useState<ParkedVehicle | null>(null);
    const exitTime = useRef<Date>(new Date());
    const queryClient = useQueryClient();

    const { data: parkedVehicles, isLoading: isLoadingVehicles } = useQuery<{ data: ParkedVehicle[] }>({
        queryKey: ['parked-vehicles'],
        queryFn: async () => {
            const response = await getParkedVehicles();
            exitTime.current = new Date();
            return { data: response };
        },
    });

    const { data: feeData, isLoading: isLoadingFee } = useQuery({
        queryKey: ['parking-fee', selectedVehicle?.id, exitTime.current],
        queryFn: async () => {
            if (!selectedVehicle) return null;
            return calculateParkingFee({
                record_id: selectedVehicle.id.toString(),
                exit_time: exitTime.current.toISOString(),
            });
        },
        enabled: !!selectedVehicle,
    });

    const form = useForm<FormData>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            record_id: '',
        },
    });

    const { mutate: registerExitMutation, isPending: isRegistering } = useMutation({
        mutationFn: registerExit,
        onSuccess: () => {
            setIsOpen(false);
            form.reset();
            queryClient.invalidateQueries();
            toast.success('Saída registrada com sucesso!');
        },
        onError: (error) => {
            toast.error('Erro ao registrar saída. Tente novamente.');
            console.error('Error registering exit:', error);
        },
    });

    function onSubmit(data: FormData) {
        registerExitMutation({
            record_id: data.record_id,
            exit_time: exitTime.current.toISOString(),
        });
    }

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogTrigger asChild>
                <Button variant="outline">Registrar Saída</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Registrar Saída</DialogTitle>
                </DialogHeader>
                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                        <FormField
                            control={form.control}
                            name="record_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Veículo</FormLabel>
                                    <Select 
                                        onValueChange={(value) => {
                                            field.onChange(value);
                                            const vehicle = parkedVehicles?.data?.find(v => v.id.toString() === value);
                                            setSelectedVehicle(vehicle || null);
                                        }} 
                                        defaultValue={field.value}
                                    >
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione um veículo" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            {isLoadingVehicles ? (
                                                <div className="flex items-center justify-center p-4">
                                                    <Loader2 className="h-4 w-4 animate-spin" />
                                                </div>
                                            ) : parkedVehicles?.data?.length === 0 ? (
                                                <div className="p-4 text-center text-sm text-muted-foreground">
                                                    Nenhum veículo estacionado
                                                </div>
                                            ) : (
                                                parkedVehicles?.data?.map((vehicle) => (
                                                    <SelectItem key={vehicle.id} value={vehicle.id.toString()}>
                                                        {vehicle.plate} - {vehicle.brand} {vehicle.model} ({vehicle.color})
                                                    </SelectItem>
                                                ))
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        {selectedVehicle && (
                            <div className="space-y-2">
                                <div className="text-sm text-muted-foreground">
                                    Entrada: {new Date(selectedVehicle.entry_time).toLocaleString()}
                                </div>
                                {isLoadingFee ? (
                                    <div className="flex items-center gap-2 text-sm">
                                        <Loader2 className="h-4 w-4 animate-spin" />
                                        Calculando valor...
                                    </div>
                                ) : feeData && (
                                    <div className="text-lg font-semibold">
                                        Valor a pagar: R$ {feeData.data.formatted_amount}
                                    </div>
                                )}
                            </div>
                        )}
                        <div className="flex justify-end">
                            <Button type="submit" disabled={isRegistering || isLoadingVehicles || isLoadingFee}>
                                {isRegistering && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                Registrar
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
} 