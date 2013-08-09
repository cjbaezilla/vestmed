CREATE TABLE [dbo].[tds_mod](
	[spf_cod_mod] [char](3) NOT NULL,
	[spf_cod_opc] [smallint] NOT NULL,
	[spf_nom_opcmod] [char](20) NOT NULL,
 CONSTRAINT [UPK_tds_mod] PRIMARY KEY CLUSTERED 
 (
	[spf_cod_mod] ,
	[spf_cod_opc] 
 )
)
GO

CREATE TABLE [dbo].[tds_prf_mod](
	[spf_cod_prf] [smallint] NOT NULL,
	[spf_cod_mod] [char](3) NOT NULL,
	[spf_opc_val] [char](10) NOT NULL
) 
GO

CREATE TABLE [dbo].[tds_prf_opcmod](
	[spf_cod_prf] [smallint] NOT NULL,
	[spf_cod_mod] [smallint] NOT NULL,
	[spf_cod_opc] [smallint] NOT NULL,
 CONSTRAINT [PK_tds_prf_opcmod] PRIMARY KEY CLUSTERED 
 (
	[spf_cod_prf] ,
	[spf_cod_mod] ,
	[spf_cod_opc] 
 )
) 
Go

CREATE TABLE [dbo].[tds_prf](
	[spf_cod_prf] [smallint] NOT NULL,
	[spf_nom_prf] [char](20) NOT NULL,
 CONSTRAINT [PK_tds_prf] PRIMARY KEY CLUSTERED 
 (
	[spf_cod_prf] ASC
 )
)
GO

CREATE TABLE [dbo].[tds_usr](
	[spf_cod_usr] [char](30) NOT NULL,
	[spf_nom_usr] [char](40) NOT NULL,
	[spf_cod_prf] [smallint] NOT NULL,
	[spf_feccre_usr] [smalldatetime] NOT NULL,
	[spf_feccad_usr] [smalldatetime] NULL,
	[spf_blk_usr] [char](1) NULL,
	[spf_tip_usr] [char](1) NULL,
	[spf_cod_suc] [smallint] NULL,
 CONSTRAINT [UPK_tds_usr] PRIMARY KEY CLUSTERED 
 (
	[spf_cod_usr] 
 )
) 

GO
ALTER TABLE [dbo].[tds_usr]  WITH CHECK ADD  CONSTRAINT [FKtds_usr_prf] FOREIGN KEY([spf_cod_prf])
      REFERENCES [dbo].[tds_prf] ([spf_cod_prf])
GO
ALTER TABLE [dbo].[tds_usr] CHECK CONSTRAINT [FKtds_usr_prf]
go
