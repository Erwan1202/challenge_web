--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

            BEGIN

                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);

                RETURN NEW;

            END;

        $$;


ALTER FUNCTION public.notify_messenger_messages() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: compte_bancaire; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.compte_bancaire (
    id integer NOT NULL,
    utilisateur_id integer NOT NULL,
    numero_de_compte integer NOT NULL,
    type character varying(255) NOT NULL,
    solde double precision NOT NULL,
    decouvert_autorise double precision NOT NULL,
    CONSTRAINT check_decouvert_autorise CHECK (((((type)::text = 'Epargne'::text) AND (decouvert_autorise = (0)::double precision)) OR (((type)::text = 'Courant'::text) AND (decouvert_autorise <= (200)::double precision))))
);


ALTER TABLE public.compte_bancaire OWNER TO postgres;

--
-- Name: compte_bancaire_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.compte_bancaire_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.compte_bancaire_id_seq OWNER TO postgres;

--
-- Name: compte_bancaire_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.compte_bancaire_id_seq OWNED BY public.compte_bancaire.id;


--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.messenger_messages OWNER TO postgres;

--
-- Name: COLUMN messenger_messages.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.messenger_messages.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.available_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.messenger_messages.available_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.delivered_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)';


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messenger_messages_id_seq OWNER TO postgres;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: transaction; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaction (
    id integer NOT NULL,
    type character varying(50) NOT NULL,
    montant double precision NOT NULL,
    date_heure timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    from_account_id integer,
    to_account_id integer,
    cancel boolean NOT NULL,
    label character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.transaction OWNER TO postgres;

--
-- Name: transaction_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transaction_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transaction_id_seq OWNER TO postgres;

--
-- Name: transaction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaction_id_seq OWNED BY public.transaction.id;


--
-- Name: utilisateur; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.utilisateur (
    id integer NOT NULL,
    nom character varying(50) NOT NULL,
    prenom character varying(50) NOT NULL,
    email character varying(255) NOT NULL,
    mdp_chiffre text NOT NULL,
    roles json DEFAULT '["ROLE_USER"]'::json NOT NULL
);


ALTER TABLE public.utilisateur OWNER TO postgres;

--
-- Name: utilisateur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.utilisateur_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.utilisateur_id_seq OWNER TO postgres;

--
-- Name: utilisateur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.utilisateur_id_seq OWNED BY public.utilisateur.id;


--
-- Name: compte_bancaire id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compte_bancaire ALTER COLUMN id SET DEFAULT nextval('public.compte_bancaire_id_seq'::regclass);


--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Name: transaction id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction ALTER COLUMN id SET DEFAULT nextval('public.transaction_id_seq'::regclass);


--
-- Name: utilisateur id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateur ALTER COLUMN id SET DEFAULT nextval('public.utilisateur_id_seq'::regclass);


--
-- Data for Name: compte_bancaire; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.compte_bancaire (id, utilisateur_id, numero_de_compte, type, solde, decouvert_autorise) FROM stdin;
1	1	123456	Courant	500	200
2	1	654321	Epargne	1000	0
3	2	789123	Courant	300	200
4	2	321987	Epargne	1500	0
\.


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
\.


--
-- Data for Name: transaction; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transaction (id, type, montant, date_heure, from_account_id, to_account_id, cancel, label) FROM stdin;
\.


--
-- Data for Name: utilisateur; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.utilisateur (id, nom, prenom, email, mdp_chiffre, roles) FROM stdin;
1	John	Doe	john.doe@example.com	hashedpassword123	["ROLE_CLIENT"]
2	Jane	Smith	jane.smith@example.com	hashedpassword456	["ROLE_CLIENT"]
4	Lebel	Vincent	vincent.lebel@efrei.net	$2y$13$hZbJfkZOlfePOzF0t/adXuHV7gKJpuLLTftoeBU8NLHo.BAdnjdze	["ROLE_CLIENT"]
5	Vincent	azerty	test@gmail.com	$2y$13$10OwbzLs.fwI6ugkbs2VT.PvvkhLhwlJC8xOxJ/adhLZGgh54cG7i	["ROLE_CLIENT"]
6	Lebel	Vincent	vlebel946@gmail.com	$2y$13$pd4vOqnL88IdGlGQ60n4Zu83QOZ/JyIKxDpMxi80dCkCQKy6YX6/W	["ROLE_CLIENT"]
7	test	test	test1@gmail.com	$2y$13$ZmiPerY2VaBN2u/MbSm6ue3St0yRBeI.z1xhl5KWuNJHjlsldvuv.	["ROLE_CLIENT"]
8	Lebel	Vincent	v@gmail.com	$2y$13$Z7aszmjS3zcArlCdtZSzJe7/Pd0E.d2xQeOkWiWO9F25JvicAS6.S	["ROLE_CLIENT"]
3	Admin	User	admin@example.com	hashedpassword789	["ROLE_ADMIN"]
\.


--
-- Name: compte_bancaire_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.compte_bancaire_id_seq', 1, false);


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);


--
-- Name: transaction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transaction_id_seq', 2, true);


--
-- Name: utilisateur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utilisateur_id_seq', 8, true);


--
-- Name: compte_bancaire compte_bancaire_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compte_bancaire
    ADD CONSTRAINT compte_bancaire_pkey PRIMARY KEY (id);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: transaction transaction_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction
    ADD CONSTRAINT transaction_pkey PRIMARY KEY (id);


--
-- Name: utilisateur utilisateur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateur
    ADD CONSTRAINT utilisateur_pkey PRIMARY KEY (id);


--
-- Name: idx_50bc21defb88e14f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_50bc21defb88e14f ON public.compte_bancaire USING btree (utilisateur_id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- Name: compte_bancaire fk_50bc21defb88e14f; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compte_bancaire
    ADD CONSTRAINT fk_50bc21defb88e14f FOREIGN KEY (utilisateur_id) REFERENCES public.utilisateur(id);


--
-- PostgreSQL database dump complete
--

