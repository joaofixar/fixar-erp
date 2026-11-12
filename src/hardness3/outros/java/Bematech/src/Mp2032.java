import java.applet.*;
import java.awt.*;
import com.sun.jna.Native;
import com.sun.jna.Library;

interface Bematech extends Library {  
	public int AcionaGuilhotina(int modo);
	public int AjustaLarguraPapel(int bitola);
	public int BematechTX(String texto);
	public int Le_Status();
	public int ConfiguraModeloImpressora(int modelo);
	public int IniciaPorta(String porta);
	public int FechaPorta();
	public int FormataTX(String BufTras, int tipoletra, int italic, int sublin, int expand, int enfat);
	public int ImprimeBitmap(String bitmap, int orientacao);
	public int SelecionaQualidadeImpressao(int qualidade);
	public int ConfiguraCodigoBarras(int altura, int largura, int pos, int fonte, int margem);
	public int ImprimeCodigoBarrasCODE128(String codbarra);
	public int ImprimeCodigoBarrasCODE39(String codbarra);
}

public class Mp2032 extends Applet
{
	private Bematech lib;
	
	public void paint(Graphics g)
	{
		g.drawString("Hardness Sistemas",5,10);
	}
	
	public void init() {
		//System.loadLibrary("jnidispatch");
		//System.setProperty("jna.library.path", "c:\\windows\\system32");
		//System.setProperty("java.library.path", "c:\\windows\\system32");
		//System.out.println(Native.getWebStartLibraryPath("jnidispatch"));
        lib = (Bematech) Native.loadLibrary("mp2032", Bematech.class);
        /*
        int retorno;
        retorno = lib.Le_Status();
        System.out.println("Le_Status: " + retorno);
        retorno = lib.ConfiguraModeloImpressora(0);
        System.out.println("ConfiguraModeloImpressora: " + retorno);
        retorno = lib.IniciaPorta("USB");
        System.out.println("USB: " + retorno);
        retorno = lib.FormataTX("TESTE DE IMPRESSÃO\r\n", 2, 0, 0, 0, 0);
        System.out.println("TESTE DE IMPRESSÃO: " + retorno);
        retorno = lib.FechaPorta();
        System.out.println("FechaPorta: " + retorno);
        */
	}
	
	public int AcionaGuilhotina(int modo) {
		return lib.AcionaGuilhotina(modo);
	}
	public int AjustaLarguraPapel(int bitola) {
		return lib.AjustaLarguraPapel(bitola);
	}
	public int BematechTX(String texto) {
		return lib.BematechTX(texto);
	}
	public int Le_Status() {
		return lib.Le_Status();
	}
	public int ConfiguraModeloImpressora(int modelo) {
		return lib.ConfiguraModeloImpressora(modelo);
	}
	public int IniciaPorta(String porta) {
		return lib.IniciaPorta(porta);
	}
	public int FechaPorta() {
		return lib.FechaPorta();
	}
	public int FormataTX(String BufTras, int tipoletra, int italic, int sublin, int expand, int enfat) {
		return lib.FormataTX(BufTras, tipoletra, italic, sublin, expand, enfat);
	}
	public int ImprimeBitmap(String bitmap, int orientacao) {
		return lib.ImprimeBitmap(bitmap, orientacao);
	}
	public int SelecionaQualidadeImpressao(int qualidade) {
		return lib.SelecionaQualidadeImpressao(qualidade);
	}
	public int ConfiguraCodigoBarras(int altura, int largura, int pos, int fonte, int margem) {
		return lib.ConfiguraCodigoBarras(altura, largura, pos, fonte, margem);
	}
	public int ImprimeCodigoBarrasCODE128(String codbarra) {
		return lib.ImprimeCodigoBarrasCODE128(codbarra);
	}
	public int ImprimeCodigoBarrasCODE39(String codbarra) {
		return lib.ImprimeCodigoBarrasCODE39(codbarra);
	}
}
