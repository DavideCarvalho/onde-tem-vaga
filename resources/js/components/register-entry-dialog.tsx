import { fetchCarBrands, fetchCarModels, fetchPricingOptions, registerEntry } from '@/api';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Loader2 } from 'lucide-react';
import { useState } from 'react';
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

interface Option {
    value: string;
    label: string;
}

interface ColorOption extends Option {
    color: string;
}

const formSchema = z.object({
    plate: z
        .string()
        .min(7, 'A placa deve ter 7 caracteres')
        .max(7, 'A placa deve ter 7 caracteres')
        .transform((val) => val.toUpperCase()),
    brand: z.string().min(1, 'Selecione uma marca'),
    model: z.string().min(1, 'Selecione um modelo'),
    color: z.string().min(1, 'Selecione uma cor'),
    pricingType: z.string().min(1, 'Selecione um tipo de preço'),
});

type FormData = z.infer<typeof formSchema>;

export function RegisterEntryDialog() {
    const [isOpen, setIsOpen] = useState(false);
    const queryClient = useQueryClient();

    const { data: carBrands = [], isLoading: isLoadingBrands } = useQuery({
        queryKey: ['carBrands'],
        queryFn: fetchCarBrands,
    });

    const { data: pricingOptions = [], isLoading: isLoadingPricing } = useQuery({
        queryKey: ['pricingOptions'],
        queryFn: fetchPricingOptions,
    });

    const [selectedBrand, setSelectedBrand] = useState('');

    const { data: carModels = [], isLoading: isLoadingModels } = useQuery({
        queryKey: ['carModels', selectedBrand],
        queryFn: () => fetchCarModels(selectedBrand),
        enabled: !!selectedBrand,
    });

    const form = useForm<FormData>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            plate: '',
            brand: '',
            model: '',
            color: '',
            pricingType: '',
        },
    });

    const brandOptions: Option[] = carBrands.map((brand) => ({
        value: brand.valor,
        label: brand.nome,
    }));

    const modelOptions: Option[] = carModels.map((model) => ({
        value: model.modelo,
        label: model.modelo,
    }));

    const colorOptions: ColorOption[] = [
        { value: 'branco', label: 'Branco', color: '#FFFFFF' },
        { value: 'preto', label: 'Preto', color: '#000000' },
        { value: 'prata', label: 'Prata', color: '#C0C0C0' },
        { value: 'cinza', label: 'Cinza', color: '#808080' },
        { value: 'vermelho', label: 'Vermelho', color: '#FF0000' },
        { value: 'azul', label: 'Azul', color: '#0000FF' },
        { value: 'verde', label: 'Verde', color: '#008000' },
        { value: 'amarelo', label: 'Amarelo', color: '#FFFF00' },
        { value: 'laranja', label: 'Laranja', color: '#FFA500' },
        { value: 'marrom', label: 'Marrom', color: '#8B4513' },
        { value: 'bege', label: 'Bege', color: '#F5F5DC' },
        { value: 'dourado', label: 'Dourado', color: '#FFD700' },
    ];

    const { mutate: registerEntryMutation, isPending: isRegistering } = useMutation({
        mutationFn: async (data: FormData) => {
            return registerEntry({
                ...data,
                pricing_type: data.pricingType,
            });
        },
        onSuccess: () => {
            setIsOpen(false);
            form.reset();
            queryClient.invalidateQueries();
            toast.success('Entrada registrada com sucesso!');
        },
        onError: (error) => {
            toast.error('Erro ao registrar entrada. Tente novamente.');
            console.error('Error registering entry:', error);
        },
    });

    function onSubmit(data: FormData) {
        registerEntryMutation(data);
    }

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogTrigger asChild>
                <Button>Registrar Nova Entrada</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Registrar Nova Entrada</DialogTitle>
                </DialogHeader>
                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                        <FormField
                            control={form.control}
                            name="plate"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Placa</FormLabel>
                                    <FormControl>
                                        <Input {...field} placeholder="Digite a placa do veículo" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="brand"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Marca</FormLabel>
                                    <FormControl>
                                        <div className="relative">
                                            <Combobox
                                                modal={true}
                                                options={brandOptions}
                                                value={field.value}
                                                onChange={(value) => {
                                                    field.onChange(value);
                                                    form.setValue('model', '');
                                                    setSelectedBrand(value);
                                                }}
                                                placeholder="Selecione a marca"
                                                emptyMessage="Nenhuma marca encontrada"
                                            />
                                            {isLoadingBrands && (
                                                <div className="absolute top-1/2 right-3 -translate-y-1/2">
                                                    <Loader2 className="h-4 w-4 animate-spin" />
                                                </div>
                                            )}
                                        </div>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="model"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Modelo</FormLabel>
                                    <FormControl>
                                        <div className="relative">
                                            <Combobox
                                                modal={true}
                                                options={modelOptions}
                                                value={field.value}
                                                onChange={field.onChange}
                                                placeholder="Selecione o modelo"
                                                emptyMessage="Nenhum modelo encontrado"
                                                disabled={!form.watch('brand')}
                                            />
                                            {isLoadingModels && (
                                                <div className="absolute top-1/2 right-3 -translate-y-1/2">
                                                    <Loader2 className="h-4 w-4 animate-spin" />
                                                </div>
                                            )}
                                        </div>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="color"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Cor</FormLabel>
                                    <FormControl>
                                        <Combobox<ColorOption>
                                            modal={true}
                                            options={colorOptions}
                                            value={field.value}
                                            onChange={field.onChange}
                                            placeholder="Selecione a cor"
                                            emptyMessage="Nenhuma cor encontrada"
                                            renderOption={(option) => (
                                                <div className="flex items-center gap-2">
                                                    <div
                                                        className="h-4 w-4 rounded-sm border border-gray-300"
                                                        style={{ backgroundColor: option.color }}
                                                    />
                                                    <span>{option.label}</span>
                                                </div>
                                            )}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="pricingType"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Tipo de Preço</FormLabel>
                                    <FormControl>
                                        <div className="relative">
                                            <Combobox
                                                modal={true}
                                                options={pricingOptions}
                                                value={field.value}
                                                onChange={field.onChange}
                                                placeholder="Selecione o tipo de preço"
                                                emptyMessage="Nenhum tipo de preço encontrado"
                                            />
                                            {isLoadingPricing && (
                                                <div className="absolute top-1/2 right-3 -translate-y-1/2">
                                                    <Loader2 className="h-4 w-4 animate-spin" />
                                                </div>
                                            )}
                                        </div>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <div className="flex justify-end">
                            <Button type="submit" disabled={isRegistering}>
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
